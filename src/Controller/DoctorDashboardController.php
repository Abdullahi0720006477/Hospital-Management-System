<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\DoctorRepository;
use App\Repository\AppointmentRepository;
use App\Repository\MedicalRecordRepository;
use App\Service\ClinicalIntelligenceService;

class DoctorDashboardController extends AbstractController
{
    #[Route('/doctor/dashboard', name: 'app_doctor_dashboard')]
    public function index(
        DoctorRepository $doctorRepository, 
        AppointmentRepository $appointmentRepository, 
        MedicalRecordRepository $medicalRecordRepository,
        ClinicalIntelligenceService $intelligenceService
    ): Response
    {
        $user = $this->getUser();
        $doctor = $doctorRepository->findOneBy(['user' => $user]);

        // If user is admin but not a doctor, show them the first doctor's dashboard as a preview
        if (!$doctor && $this->isGranted('ROLE_ADMIN')) {
            $doctor = $doctorRepository->findOneBy([]);
        }

        if (!$doctor) {
            throw $this->createNotFoundException('Doctor profile not found. Please create a doctor profile first.');
        }

        $allAppointments = $appointmentRepository->findBy(['doctor' => $doctor], ['appointmentDate' => 'DESC']);
        $pendingCount = $appointmentRepository->count(['doctor' => $doctor, 'status' => 'Pending']);
        
        // Stats
        $monthlyEarnings = 0;
        foreach ($allAppointments as $app) {
            if ($app->getStatus() === 'completed' && $app->getAppointmentDate()->format('m-Y') === date('m-Y')) {
                $monthlyEarnings += (float)$doctor->getConsultationFee();
            }
        }

        // Weekly Workload (Last 7 days)
        $workload_data = [];
        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = (new \DateTime())->modify("-$i days");
            $days[] = $date->format('D');
            $workload_data[] = $appointmentRepository->countByDate($date->format('Y-m-d'), $doctor); 
        }

        // Patient Types (Simulated distribution)
        $patient_types = [
            'new' => rand(20, 40),
            'followup' => rand(40, 60),
            'emergency' => rand(5, 15)
        ];

        // Process Appointments with Risk Scoring
        $appointmentsWithRisk = [];
        $recentAppointments = array_slice($allAppointments, 0, 10);
        foreach ($recentAppointments as $appointment) {
            $lastRecord = $medicalRecordRepository->findOneBy(['patient' => $appointment->getPatient()], ['createdAt' => 'DESC']);
            $risk = $lastRecord ? $intelligenceService->calculateRiskScore($lastRecord) : ['score' => 0, 'level' => 'No Data', 'color' => 'secondary'];
            
            $appointmentsWithRisk[] = [
                'entity' => $appointment,
                'risk' => $risk
            ];
        }

        // Recent Medical Records
        $recentRecords = $medicalRecordRepository->createQueryBuilder('m')
            ->join('m.appointment', 'a')
            ->where('a.doctor = :doctor')
            ->setParameter('doctor', $doctor)
            ->orderBy('m.createdAt', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        return $this->render('doctor_dashboard/index.html.twig', [
            'doctor' => $doctor,
            'appointments' => $appointmentsWithRisk, 
            'recent_records' => $recentRecords,
            'pending_count' => $pendingCount,
            'total_appointments' => count($allAppointments),
            'monthly_earnings' => $monthlyEarnings,
            'workload_data' => $workload_data,
            'workload_labels' => $days,
            'patient_types' => $patient_types,
            'satisfaction_score' => rand(88, 99)
        ]);
    }
}
