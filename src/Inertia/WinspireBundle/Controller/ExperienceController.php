<?php

namespace Inertia\WinspireBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ExperienceController extends Controller
{
    public function extendedFooterAction()
    {
        $repository = $this->getDoctrine()
            ->getEntityManager()
            ->getRepository('InertiaWinspireBundle:Experience');
        $query = $repository->createQueryBuilder('e')
            ->where('e.is_featured = true')
            ->andWhere('e.is_active = true')
            ->getQuery();
        $featured = $query->getResult();
        
        $repository = $this->getDoctrine()
            ->getEntityManager()
            ->getRepository('InertiaWinspireBundle:Category');
        $query = $repository->createQueryBuilder('c')
            ->where('c.is_active = true')
            ->orderBy('c.name', 'ASC')
            ->getQuery();
        $categories = $query->getResult();
        
        return $this->render('InertiaWinspireBundle::extendedFooter.html.twig',
            array(
                'featured' => $featured,
                'categories' => $categories,
            )
        );
    }
}
