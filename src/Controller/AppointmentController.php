<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Form\AppointmentType;
use App\Repository\AppointmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/appointment')]
class AppointmentController extends AbstractController
{
    #[Route('/', name: 'app_appointment_index', methods: ['GET'])]
    public function index(AppointmentRepository $appointmentRepository): Response
    {
        return $this->render('appointment/index.html.twig', [
            'appointments' => $appointmentRepository->findBy([], ['appointmentDate' => 'DESC']),
        ]);
    }

    #[Route('/calendar', name: 'app_appointment_calendar', methods: ['GET'])]
    public function calendar(AppointmentRepository $appointmentRepository): Response
    {
        $appointments = $appointmentRepository->findAll();
        $events = [];

        foreach ($appointments as $appointment) {
            $events[] = [
                'id' => $appointment->getId(),
                'title' => $appointment->getPatient()->getUser()->getFullName() . ' w/ ' . $appointment->getDoctor()->getUser()->getFullName(),
                'start' => $appointment->getAppointmentDate()->format(\DateTimeInterface::ATOM),
                'color' => $this->getStatusColor($appointment->getStatus()),
                'extendedProps' => [
                    'patient' => $appointment->getPatient()->getUser()->getFullName(),
                    'doctor' => $appointment->getDoctor()->getUser()->getFullName(),
                    'status' => $appointment->getStatus(),
                ]
            ];
        }

        return $this->render('appointment/calendar.html.twig', [
            'events' => $events,
        ]);
    }

    private function getStatusColor(string $status): string
    {
        return match ($status) {
            'confirmed' => '#4e73df',
            'completed' => '#1cc88a',
            'cancelled' => '#e74a3b',
            default => '#858796',
        };
    }

    #[Route('/new', name: 'app_appointment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AppointmentRepository $appointmentRepository): Response
    {
        $appointment = new Appointment();
        $form = $this->createForm(AppointmentType::class, $appointment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$appointment->getTokenNumber()) {
                $appointment->setTokenNumber('T-'.time());
            }
            
            $appointmentRepository->save($appointment, true);

            return $this->redirectToRoute('app_appointment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('appointment/new.html.twig', [
            'appointment' => $appointment,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_appointment_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Appointment $appointment): Response
    {
        return $this->render('appointment/show.html.twig', [
            'appointment' => $appointment,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_appointment_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Appointment $appointment, AppointmentRepository $appointmentRepository): Response
    {
        $form = $this->createForm(AppointmentType::class, $appointment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $appointmentRepository->save($appointment, true);

            return $this->redirectToRoute('app_appointment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('appointment/edit.html.twig', [
            'appointment' => $appointment,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/status/{status}', name: 'app_appointment_status_update', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function updateStatus(Request $request, Appointment $appointment, string $status, AppointmentRepository $appointmentRepository): Response
    {
        if ($this->isCsrfTokenValid('status' . $appointment->getId(), $request->request->get('_token'))) {
            $appointment->setStatus($status);
            $appointmentRepository->save($appointment, true);
        }

        return $this->redirectToRoute('app_appointment_index');
    }
}
