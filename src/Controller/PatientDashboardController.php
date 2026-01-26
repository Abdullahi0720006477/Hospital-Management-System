<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\PatientRepository;
use App\Repository\AppointmentRepository;
use App\Repository\MedicalRecordRepository;

class PatientDashboardController extends AbstractController
{
    #[Route('/patient/dashboard', name: 'app_patient_dashboard')]
    public function index(
        PatientRepository $patientRepository, 
        AppointmentRepository $appointmentRepository,
        MedicalRecordRepository $medicalRecordRepository
    ): Response {
        $user = $this->getUser();
        $patient = $patientRepository->findOneBy(['user' => $user]);

        // If user is admin but not a patient, show them the first patient's dashboard as a preview
        if (!$patient && $this->isGranted('ROLE_ADMIN')) {
            $patient = $patientRepository->findOneBy([]);
        }

        if (!$patient) {
            throw $this->createNotFoundException('Patient profile not found. Please register a patient first.');
        }

        $upcomingAppointments = $appointmentRepository->findBy(
            ['patient' => $patient, 'status' => ['pending', 'confirmed']], 
            ['appointmentDate' => 'ASC']
        );
        
        $medicalRecords = $medicalRecordRepository->findBy(
            ['patient' => $patient], 
            ['createdAt' => 'DESC']
        );

        // simulated vitals history for charts
        $vitals_history = [
            'dates' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            'weight' => [70, 71, 70.5, 69.8, 69.2, 68.5],
            'bp_systolic' => [120, 122, 118, 121, 119, 117]
        ];

        // Latest record vitals
        $latestVitals = !empty($medicalRecords) ? $medicalRecords[0]->getVitalSigns() : null;

        return $this->render('patient_dashboard/index.html.twig', [
            'patient' => $patient,
            'upcoming_appointments' => $upcomingAppointments,
            'medical_records' => array_slice($medicalRecords, 0, 5),
            'total_appointments' => $appointmentRepository->count(['patient' => $patient]),
            'vitals_history' => $vitals_history,
            'latest_vitals' => $latestVitals,
            'health_score' => rand(85, 95)
        ]);
    }
}
