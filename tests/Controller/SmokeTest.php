<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SmokeTest extends WebTestCase
{
    public function testPublicPagesAreUp(): void
    {
        $client = static::createClient();
        
        // Home
        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        
        // Login
        $client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
    }

    public function testProtectedPagesRedirectToLogin(): void
    {
        $client = static::createClient();
        
        $urls = [
            '/admin/dashboard',
            '/doctor/dashboard',
            '/patient/dashboard',
            '/appointment/',
            '/doctor/',
            '/patient/'
        ];

        foreach ($urls as $url) {
            $client->request('GET', $url);
            // Should redirect to login. We check if it redirects to /login or absolute url ending in /login
            $isRedirect = $client->getResponse()->isRedirect();
            $this->assertTrue($isRedirect, "URL '$url' did not redirect to login. Status code: " . $client->getResponse()->getStatusCode());
            
            $location = $client->getResponse()->headers->get('Location');
            $this->assertStringContainsString('/login', $location, "URL '$url' redirected to '$location' instead of login.");
        }
    }
}
