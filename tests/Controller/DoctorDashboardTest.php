<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DoctorDashboardTest extends WebTestCase
{
    public function testDoctorDashboardAccess(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // Retrieve the test doctor user
        $testDoctor = $userRepository->findOneByEmail('doctor@test.com');

        // Simulate login
        $client->loginUser($testDoctor);

        // Test dashboard access
        $crawler = $client->request('GET', '/doctor/dashboard');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Dr. Greg House!');
        $this->assertSelectorExists('.doctor-hero');
        $this->assertSelectorExists('.glass-card');
        $this->assertSelectorExists('canvas#workloadChart');
    }

    public function testDoctorDashboardUnauthorized(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // Retrieve the test patient user (should not have access to doctor dashboard)
        $testPatient = $userRepository->findOneByEmail('patient@test.com');

        $client->loginUser($testPatient);

        $client->request('GET', '/doctor/dashboard');
        
        // It might be a 403 or redirect depending on how security is configured
        // Usually, AccessDeniedException results in 403 for logged in users
        $this->assertResponseStatusCodeSame(403);
    }
}
