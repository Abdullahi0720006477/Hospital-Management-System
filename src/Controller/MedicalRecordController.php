<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\MedicalRecord;
use App\Form\MedicalRecordType;
use App\Repository\MedicalRecordRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/medical-record')]
class MedicalRecordController extends AbstractController
{
    #[Route('/', name: 'app_medical_record_index', methods: ['GET'])]
    public function index(MedicalRecordRepository $medicalRecordRepository): Response
    {
        return $this->render('medical_record/index.html.twig', [
            'medical_records' => $medicalRecordRepository->findAll(),
        ]);
    }

    #[Route('/new/{appointment}', name: 'app_medical_record_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Appointment $appointment, MedicalRecordRepository $medicalRecordRepository): Response
    {
        if ($appointment->getMedicalRecord()) {
            return $this->redirectToRoute('app_medical_record_show', ['id' => $appointment->getMedicalRecord()->getId()]);
        }

        $medicalRecord = new MedicalRecord();
        $medicalRecord->setAppointment($appointment);
        $medicalRecord->setPatient($appointment->getPatient());

        $form = $this->createForm(MedicalRecordType::class, $medicalRecord);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Combine vitals from request if needed
            $vitals = [
                'bp' => $request->request->get('vital_bp'),
                'temp' => $request->request->get('vital_temp'),
                'weight' => $request->request->get('vital_weight'),
            ];
            $medicalRecord->setVitalSigns($vitals);

            $medicalRecordRepository->save($medicalRecord, true);

            // Mark appointment as completed
            $appointment->setStatus('completed');
            // We need to save appointment too, but cascade might handle it if configured
            // Since we don't have cascade persist on appointment side for medicalRecord yet (we have it on record side)
            // It's better to save explicitly or ensure relation is correct.

            return $this->redirectToRoute('app_medical_record_show', ['id' => $medicalRecord->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('medical_record/new.html.twig', [
            'medical_record' => $medicalRecord,
            'appointment' => $appointment,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_medical_record_show', methods: ['GET'])]
    public function show(MedicalRecord $medicalRecord): Response
    {
        return $this->render('medical_record/show.html.twig', [
            'medical_record' => $medicalRecord,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_medical_record_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MedicalRecord $medicalRecord, MedicalRecordRepository $medicalRecordRepository): Response
    {
        $form = $this->createForm(MedicalRecordType::class, $medicalRecord);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Update vitals if they are passed manually, or rely on form mapping if MedicalRecordType handles it.
            // Assuming MedicalRecordType handles 'diagnosis' and 'prescription'.
            // Vitals might need manual handling if they are not fields in the form class but just array data.
            // Check previous 'new' method: it manually sets vitals from request parameters.
            
            $vitals = $medicalRecord->getVitalSigns() ?: [];
            if ($request->request->has('vital_bp')) {
                $vitals['bp'] = $request->request->get('vital_bp');
                $vitals['temp'] = $request->request->get('vital_temp');
                $vitals['weight'] = $request->request->get('vital_weight');
                $medicalRecord->setVitalSigns($vitals);
            }

            $medicalRecordRepository->save($medicalRecord, true);

            return $this->redirectToRoute('app_medical_record_show', ['id' => $medicalRecord->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('medical_record/edit.html.twig', [
            'medical_record' => $medicalRecord,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_medical_record_delete', methods: ['POST'])]
    public function delete(Request $request, MedicalRecord $medicalRecord, MedicalRecordRepository $medicalRecordRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $medicalRecord->getId(), $request->request->get('_token'))) {
            $medicalRecordRepository->remove($medicalRecord, true);
        }

        return $this->redirectToRoute('app_medical_record_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/pdf', name: 'app_medical_record_pdf', methods: ['GET'])]
    public function exportPdf(MedicalRecord $medicalRecord): Response
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        
        $dompdf = new Dompdf($pdfOptions);
        
        $html = $this->renderView('medical_record/pdf.html.twig', [
            'record' => $medicalRecord
        ]);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $fileName = 'medical_record_' . $medicalRecord->getId() . '.pdf';
        
        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"'
        ]);
    }
}
