<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\PostStatistic;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        // Mengambil 5 post dengan jumlah views paling banyak
        $topPosts = $entityManager->getRepository(Post::class)->findBy([], ['views' => 'DESC'], 5);

        // Statistik Bulanan dengan Filter
        $month = $request->query->get('month', date('m'));
        $year = $request->query->get('year', date('Y'));

        $startDate = new \DateTime("$year-$month-01 00:00:00");
        $endDate = (clone $startDate)->modify('last day of this month')->setTime(23, 59, 59);

        $monthlyRaw = $entityManager->getRepository(PostStatistic::class)
            ->createQueryBuilder('s')
            ->where('s.datetime >= :start')
            ->andWhere('s.datetime <= :end')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->getQuery()
            ->getResult();

        $monthlyLabels = [];
        $monthlyValues = [];
        $daysInMonth = (int)$endDate->format('d');
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dayKey = sprintf('%02d', $d);
            $monthlyLabels[] = $dayKey;
            $count = 0;
            foreach ($monthlyRaw as $stat) {
                if ($stat->getDatetime()->format('d') === $dayKey) {
                    $count++;
                }
            }
            $monthlyValues[] = $count;
        }

        return $this->render('dashboard/index.html.twig', [
            'topPosts' => $topPosts,
            'monthlyLabels' => $monthlyLabels,
            'monthlyValues' => $monthlyValues,
            'selectedMonth' => $month,
            'selectedYear' => $year,
        ]);
    }
}
