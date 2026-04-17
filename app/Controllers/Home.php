<?php

namespace App\Controllers;

use App\Models\GalleryModel;
use App\Models\InquiryModel;
use App\Models\ResortRatingModel;
use App\Models\ResortRoomModel;
use Config\Database;
use DateTimeImmutable;

class Home extends BaseController
{
    public function index(): string
    {
        $galleryModel = new GalleryModel();
        $ratingModel = new ResortRatingModel();
        $featured = array_slice($galleryModel->getPublicGallery(), 0, 6);
        $calendar = $this->buildAvailabilityCalendar((string) ($this->request->getGet('month') ?? ''));
        $heroGallery = $galleryModel->getHeroSlides(5);

        $ratingCount = 0;
        $averageRating = 0.0;
        $recentRatings = [];

        if ($this->ratingsTableExists()) {
            $ratingCount = (int) $ratingModel->where('is_public', 1)->countAllResults();
            $averageRow = $ratingModel
                ->select('AVG(rating) AS avg_rating')
                ->where('is_public', 1)
                ->first();
            $averageRating = (float) ($averageRow['avg_rating'] ?? 0);

            $recentRatings = $ratingModel
                ->where('is_public', 1)
                ->orderBy('created_at', 'DESC')
                ->findAll(5);
        }

        $heroSlides = array_map(
            static fn (array $image): string => base_url((string) $image['image_path']),
            $heroGallery
        );

        return view('home/index', [
            'title'      => 'White Sand Resort',
            'featured'   => $featured,
            'heroSlides' => $heroSlides,
            'bookingCalendar' => $calendar,
            'ratingSummary' => [
                'count'   => $ratingCount,
                'average' => $averageRating,
                'recent'  => $recentRatings,
            ],
        ]);
    }

    public function submitRating()
    {
        if (! $this->ratingsTableExists()) {
            return redirect()->to('/')->with('warning', 'Ratings are temporarily unavailable. Please run database migrations.');
        }

        $rules = [
            'name'    => 'required|min_length[2]|max_length[120]',
            'rating'  => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[5]',
            'comment' => 'permit_empty|max_length[500]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('/')->withInput()->with('error', 'Please provide a valid name and rating before submitting.');
        }

        $ratingModel = new ResortRatingModel();
        $ratingModel->insert([
            'name'      => trim((string) $this->request->getPost('name')),
            'rating'    => (int) $this->request->getPost('rating'),
            'comment'   => trim((string) $this->request->getPost('comment')),
            'is_public' => 1,
        ]);

        return redirect()->to('/')->with('success', 'Thank you! Your resort rating has been submitted.');
    }

    private function ratingsTableExists(): bool
    {
        try {
            return Database::connect()->tableExists('resort_ratings');
        } catch (\Throwable $exception) {
            return false;
        }
    }

    private function buildAvailabilityCalendar(string $monthParam): array
    {
        try {
            $selectedMonth = $monthParam !== ''
                ? new DateTimeImmutable($monthParam . '-01')
                : new DateTimeImmutable(date('Y-m-01'));
        } catch (\Throwable $exception) {
            $selectedMonth = new DateTimeImmutable(date('Y-m-01'));
        }

        $monthStart = $selectedMonth->modify('first day of this month');
        $monthEnd = $selectedMonth->modify('last day of this month');
        $gridStart = $monthStart->modify('-' . ((int) $monthStart->format('N') - 1) . ' days');
        $gridEnd = $monthEnd->modify('+' . (7 - (int) $monthEnd->format('N')) . ' days');

        $inquiryModel = new InquiryModel();
        $roomModel = new ResortRoomModel();

        $roomCount = (int) $roomModel->where('is_active', 1)->countAllResults();
        if ($roomCount < 1) {
            $roomCount = 3;
        }

        $bookings = $inquiryModel
            ->whereIn('request_type', ['booking', 'reservation'])
            ->where('check_in <=', $monthEnd->format('Y-m-d'))
            ->where('check_out >=', $monthStart->format('Y-m-d'))
            ->findAll();

        $countsByDate = [];
        foreach ($bookings as $booking) {
            $checkIn = (string) ($booking['check_in'] ?? '');
            $checkOut = (string) ($booking['check_out'] ?? '');

            if ($checkIn === '' || $checkOut === '') {
                continue;
            }

            try {
                $bookingStart = new DateTimeImmutable($checkIn);
                $bookingEnd = (new DateTimeImmutable($checkOut))->modify('-1 day');
            } catch (\Throwable $exception) {
                continue;
            }

            if ($bookingEnd < $bookingStart) {
                $bookingEnd = $bookingStart;
            }

            if ($bookingStart < $monthStart) {
                $bookingStart = $monthStart;
            }

            if ($bookingEnd > $monthEnd) {
                $bookingEnd = $monthEnd;
            }

            for ($cursor = $bookingStart; $cursor <= $bookingEnd; $cursor = $cursor->modify('+1 day')) {
                $dateKey = $cursor->format('Y-m-d');
                $countsByDate[$dateKey] = ($countsByDate[$dateKey] ?? 0) + 1;
            }
        }

        $weeks = [];
        for ($cursor = $gridStart; $cursor <= $gridEnd; $cursor = $cursor->modify('+1 day')) {
            $weekIndex = count($weeks) - 1;
            if ($weekIndex < 0 || count($weeks[$weekIndex]) === 7) {
                $weeks[] = [];
                $weekIndex++;
            }

            $dateKey = $cursor->format('Y-m-d');
            $count = (int) ($countsByDate[$dateKey] ?? 0);
            $status = 'available';

            if ($count > 0 && $count >= $roomCount) {
                $status = 'full';
            } elseif ($count > 0) {
                $status = 'limited';
            }

            $weeks[$weekIndex][] = [
                'date'        => $dateKey,
                'day'         => $cursor->format('j'),
                'inMonth'     => $cursor->format('m') === $monthStart->format('m'),
                'isToday'     => $cursor->format('Y-m-d') === date('Y-m-d'),
                'status'      => $status,
                'bookingCount'=> $count,
            ];
        }

        return [
            'label'      => $monthStart->format('Y.m'),
            'monthValue' => $monthStart->format('Y-m'),
            'prevMonth'  => $monthStart->modify('-1 month')->format('Y-m'),
            'nextMonth'  => $monthStart->modify('+1 month')->format('Y-m'),
            'weekdays'   => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            'weeks'      => $weeks,
            'roomCount'  => $roomCount,
        ];
    }
}
