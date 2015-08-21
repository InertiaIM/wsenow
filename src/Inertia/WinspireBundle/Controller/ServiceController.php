<?php

namespace Inertia\WinspireBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Inertia\WinspireBundle\Entity\Category;
use Inertia\WinspireBundle\Entity\Experience;
use Inertia\WinspireBundle\Entity\Page;
use Inertia\WinspireBundle\Entity\Testimonial;


class ServiceController extends Controller
{
    public function triggerAction($type, $method, $sfId)
    {
$handle = fopen('/tmp/testing.log', 'a');
fwrite($handle, $type . '/' . $method . '/' . $sfId . "\n");

        $em = $this->getDoctrine()->getEntityManager();
        $categoryRepository = $em->getRepository('InertiaWinspireBundle:Category');
        $experienceRepository = $em->getRepository('InertiaWinspireBundle:Experience');
        $pageRepository = $em->getRepository('InertiaWinspireBundle:Page');
        $testimonialRepository = $em->getRepository('InertiaWinspireBundle:Testimonial');



        switch($type)
        {
            case 'page':
                if($method == 'update')
                {
                    $query = $pageRepository->createQueryBuilder('p')
                        ->where('p.sf_id = :id')
                        ->setParameter('id', $sfId)
                        ->getQuery();
                    
                    try
                    {                    	
                        $page = $query->getSingleResult();
                        $page->setIsDirty(true);
                        $em->flush();
                    }
                    catch(\Exception $e)
                    {
                        //
                    }
                }
                
                if($method == 'insert')
                {
                    $page = new Page();
                    $page->setSfId($sfId);
                    $page->setIsActive(false);
                    $page->setIsDirty(true);
                    
                    $em->persist($page);
                    $em->flush();
                }
                
                if($method == 'delete')
                {
                    $query = $pageRepository->createQueryBuilder('p')
                        ->where('p.sf_id = :id')
                        ->setParameter('id', $sfId)
                        ->getQuery();
                    
                    try
                    {
                        $page = $query->getSingleResult();
                        $page->setIsActive(false);
                        $em->flush();
                    }
                    catch(\Exception $e)
                    {
                        //
                    }
                }
                
                break;
                
                
            case 'category':
                if($method == 'update')
                {
                    $query = $categoryRepository->createQueryBuilder('c')
                        ->where('c.sf_id = :id')
                        ->setParameter('id', $sfId)
                        ->getQuery();
                        
                    try
                    {
                        $category = $query->getSingleResult();
                        $category->setIsDirty(true);
                        $em->flush();
                    }
                    catch(\Exception $e)
                    {
                        //
                    }
                }
                
                if($method == 'insert')
                {
                    $category = new Category();
                    $category->setSfId($sfId);
                    $category->setIsActive(false);
                    $category->setIsDirty(true);
                    
                    $em->persist($category);
                    $em->flush();
                }
                
                if($method == 'delete')
                {
                    $query = $categoryRepository->createQueryBuilder('c')
                        ->where('c.sf_id = :id')
                        ->setParameter('id', $sfId)
                        ->getQuery();
                        
                    try
                    {
                        $category = $query->getSingleResult();
                        $category->setIsActive(false);
                        $em->flush();
                    }
                    catch(\Exception $e)
                    {
                        //
                    }
                }
                
                break;
                
            case 'experience':
                if($method == 'update')
                {
                    $query = $experienceRepository->createQueryBuilder('e')
                        ->where('e.sf_id = :id')
                        ->setParameter('id', $sfId)
                        ->getQuery();
                        
                    try
                    {
                        $experience = $query->getSingleResult();
                        $experience->setIsDirty(true);
                        $em->flush();
                    }
                    catch(\Exception $e)
                    {
                        //
                    }
                }
                
                if($method == 'insert')
                {
                	$category = $categoryRepository->find(1);
                	
                    $experience = new Experience();
                    $experience->setSfId($sfId);
                    $experience->setIsActive(false);
                    $experience->setIsDirty(true);
                    $experience->setCategory($category);
                    
                    $em->persist($experience);
                    $em->flush();
                }
                
                if($method == 'delete')
                {
                    $query = $experienceRepository->createQueryBuilder('e')
                        ->where('e.sf_id = :id')
                        ->setParameter('id', $sfId)
                        ->getQuery();
                        
                    try
                    {
                        $experience = $query->getSingleResult();
                        $experience->setIsActive(false);
                        $em->flush();
                    }
                    catch(\Exception $e)
                    {
                        //
                    }
                }
                
                break;
                
            case 'testimonial':
                if($method == 'update')
                {
                    $query = $testimonialRepository->createQueryBuilder('t')
                        ->where('t.sf_id = :id')
                        ->setParameter('id', $sfId)
                        ->getQuery();
                        
                    try
                    {
                        $testimonial = $query->getSingleResult();
                        $testimonial->setIsDirty(true);
                        $em->flush();
                    }
                    catch(\Exception $e)
                    {
                        //
                    }
                }
                
                if($method == 'insert')
                {
                    $testimonial = new Testimonial();
                    $testimonial->setSfId($sfId);
                    $testimonial->setIsActive(false);
                    $testimonial->setIsDirty(true);
                    
                    $em->persist($testimonial);
                    $em->flush();
                }
                
                if($method == 'delete')
                {
                    $query = $testimonialRepository->createQueryBuilder('t')
                        ->where('t.sf_id = :id')
                        ->setParameter('id', $sfId)
                        ->getQuery();
                        
                    try
                    {
                        $testimonial = $query->getSingleResult();
                        $testimonial->setIsActive(false);
                        $em->flush();
                    }
                    catch(\Exception $e)
                    {
                        //
                    }
                }
                
                break;
        }
        
fclose($handle);
        $response = new Response();
        return $response;
    }
}
