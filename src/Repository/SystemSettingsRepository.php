<?php

namespace App\Repository;

use App\Entity\SystemSettings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SystemSettings>
 */
class SystemSettingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SystemSettings::class);
    }

    public function getSettings(): SystemSettings
    {
        $settings = $this->findOneBy([]);
        if (!$settings) {
            $settings = new SystemSettings();
            $settings->setHospitalName('MediCore Hospital');
            $settings->setDashboardTitle('Hospital OS v2.0');
            $this->getEntityManager()->persist($settings);
            $this->getEntityManager()->flush();
        }
        return $settings;
    }
}
