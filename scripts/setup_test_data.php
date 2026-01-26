<?php

use App\Entity\User;
use App\Entity\Doctor;
use App\Entity\Patient;
use App\Entity\Department;
use App\Entity\Appointment;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

require dirname(__DIR__).'/vendor/autoload.php';

// This is a simplified script to be run via php bin/console or similar if possible.
// Better to create a command.

echo "Creating test data...\n";
