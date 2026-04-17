<?php

namespace App\Controllers;

use App\Models\InquiryModel;

class Inquiry extends BaseController
{
    public function index(): string
    {
        return $this->renderForm('inquiry');
    }

    public function booking(): string
    {
        return $this->renderForm('booking');
    }

    public function reservation(): string
    {
        return $this->renderForm('reservation');
    }

    public function bookingConfirmation(): string
    {
        $details = [
            'guest_name'      => 'Gil Cuyos',
            'booking_ref'     => 'GC-58-2403',
            'room_type'       => 'Barkada Room (10 - 15 pax)',
            'check_in_date'   => 'May 29, 2026',
            'check_in_time'   => '2:00 PM',
            'check_out_date'  => 'May 31, 2026',
            'check_out_time'  => '12:00 NN',
            'rate_per_night'  => 'PHP 3,899',
            'total_amount'    => 'PHP 7,798',
            'length_of_stay'  => '2 nights',
            'payment_method'  => 'Pay upon arrival',
            'notes'           => [
                'Please present a valid ID upon check-in.',
                'Check-in time and resort policies will apply.',
                'For any changes or inquiries, kindly contact the resort directly.',
            ],
        ];

        return view('booking/confirmation', [
            'title'   => 'Booking Confirmation',
            'details' => $details,
        ]);
    }

    public function submit()
    {
        $inquiryModel = new InquiryModel();
        $requestType = strtolower(trim((string) $this->request->getPost('request_type')));

        $validationRules = [
            'request_type' => 'required|in_list[booking,inquiry,reservation]',
            'name'         => 'required|min_length[2]|max_length[120]',
            'email'        => 'required|valid_email|max_length[160]',
            'phone'        => 'permit_empty|max_length[30]',
            'message'      => 'required|min_length[10]|max_length[3000]',
        ];

        if (in_array($requestType, ['booking', 'reservation'], true)) {
            $validationRules['check_in'] = 'required|valid_date[Y-m-d]';
            $validationRules['check_out'] = 'required|valid_date[Y-m-d]';
            $validationRules['guests'] = 'required|integer|greater_than_equal_to[1]|less_than_equal_to[20]';
        } else {
            $validationRules['check_in'] = 'permit_empty|valid_date[Y-m-d]';
            $validationRules['check_out'] = 'permit_empty|valid_date[Y-m-d]';
            $validationRules['guests'] = 'permit_empty|integer|greater_than_equal_to[1]|less_than_equal_to[20]';
        }

        if (! $this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('error', 'Please correct the form errors.');
        }

        $checkIn = (string) ($this->request->getPost('check_in') ?: '');
        $checkOut = (string) ($this->request->getPost('check_out') ?: '');
        if ($checkIn !== '' && $checkOut !== '' && strtotime($checkOut) < strtotime($checkIn)) {
            return redirect()->back()->withInput()->with('error', 'Check-out date must be on or after check-in date.');
        }

        $data = [
            'request_type' => $requestType,
            'name'         => trim((string) $this->request->getPost('name')),
            'email'        => strtolower(trim((string) $this->request->getPost('email'))),
            'phone'        => trim((string) $this->request->getPost('phone')),
            'check_in'     => $checkIn !== '' ? $checkIn : null,
            'check_out'    => $checkOut !== '' ? $checkOut : null,
            'guests'       => (int) ($this->request->getPost('guests') ?: 1),
            'message'      => trim((string) $this->request->getPost('message')),
            'status'       => 'pending',
        ];

        $inquiryModel->insert($data);
        $this->sendAdminNotification($data);

        return redirect()->to('/inquiry')->with('success', 'Thanks for your ' . ucfirst($data['request_type']) . '. Our team will contact you shortly.');
    }

    private function renderForm(string $requestType): string
    {
        $validTypes = ['booking', 'inquiry', 'reservation'];
        if (! in_array($requestType, $validTypes, true)) {
            $requestType = 'inquiry';
        }

        return view('inquiry/index', [
            'title'       => ucfirst($requestType) . ' Request',
            'requestType' => $requestType,
        ]);
    }

    private function sendAdminNotification(array $inquiry): void
    {
        $email = service('email');
        $config = config('Email');
        $to = (string) $config->recipients;

        if ($to === '') {
            log_message('warning', 'Inquiry email not sent: email.recipients is empty.');

            return;
        }

        $email->setTo($to);
        $email->setSubject('New Resort Inquiry Received');
        $email->setMessage(view('emails/inquiry_notification', ['inquiry' => $inquiry]));

        if (! $email->send()) {
            log_message('error', 'Inquiry email sending failed.');
        }
    }
}
