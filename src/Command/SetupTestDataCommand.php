<?php

namespace App\Command;

use App\Entity\Appointment;
use App\Entity\Department;
use App\Entity\Doctor;
use App\Entity\Patient;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:setup-test-data',
    description: 'Creates test data for doctors and patients',
)]
class SetupTestDataCommand extends Command
{
    private $entityManager;
    private $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // 1. Create Department
        $department = new Department();
        $department->setName('Cardiology');
        $department->setDescription('Heart and blood vessel specialist');
        $this->entityManager->persist($department);

        // 2. Create Doctor User
        $doctorUser = new User();
        $doctorUser->setEmail('doctor@test.com');
        $doctorUser->setFullName('Greg House');
        $doctorUser->setRoles(['ROLE_DOCTOR']);
        $doctorUser->setPassword($this->passwordHasher->hashPassword($doctorUser, 'password'));
        $this->entityManager->persist($doctorUser);

        // 3. Create Doctor Profile
        $doctor = new Doctor();
        $doctor->setUser($doctorUser);
        $doctor->setDepartment($department);
        $doctor->setSpecialization('Diagnostic Medicine');
        $doctor->setExperience(20);
        $doctor->setConsultationFee('150.00');
        $this->entityManager->persist($doctor);

        // 4. Create Patient User
        $patientUser = new User();
        $patientUser->setEmail('patient@test.com');
        $patientUser->setFullName('John Doe');
        $patientUser->setRoles(['ROLE_PATIENT']);
        $patientUser->setPassword($this->passwordHasher->hashPassword($patientUser, 'password'));
        $this->entityManager->persist($patientUser);

        // 5. Create Patient Profile
        $patient = new Patient();
        $patient->setUser($patientUser);
        $patient->setAddress('123 Main St');
        $patient->setPhone('555-0101');
        $patient->setGender('Male');
        $patient->setDateOfBirth(new \DateTime('1985-05-20'));
        $this->entityManager->persist($patient);

        // 6. Create Appointments
        for ($i = 0; $i < 15; $i++) {
            $appointment = new Appointment();
            $appointment->setPatient($patient);
            $appointment->setDoctor($doctor);
            $date = (new \DateTime())->modify("-" . rand(0, 10) . " days")->modify("+" . rand(8, 17) . " hours");
            $appointment->setAppointmentDate($date);
            $appointment->setStatus($i % 3 === 0 ? 'completed' : ($i % 3 === 1 ? 'Pending' : 'confirmed'));
            $appointment->setTokenNumber('T-' . time() . $i);
            $appointment->setNotes('Regular checkup ' . $i);
            $this->entityManager->persist($appointment);
        }

        // 7. Create Admin User
        $adminUser = new User();
        $adminUser->setEmail('admin@hms.com');
        $adminUser->setFullName('System Administrator');
        $adminUser->setRoles(['ROLE_ADMIN']);
        $adminUser->setPassword($this->passwordHasher->hashPassword($adminUser, 'password'));
        $this->entityManager->persist($adminUser);

        $this->entityManager->flush();

        $io->success('Test data created successfully!');
        $io->table(
            ['Role', 'Email', 'Password'],
            [
                ['Doctor', 'doctor@test.com', 'password'],
                ['Patient', 'patient@test.com', 'password'],
                ['Admin', 'admin@hms.com', 'password'],
            ]
        );

        return Command::SUCCESS;
    }
}
