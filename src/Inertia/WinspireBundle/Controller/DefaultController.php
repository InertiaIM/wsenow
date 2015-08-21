<?php

namespace Inertia\WinspireBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\MinLength;
use Inerita\WinspireBundle\Entity\Experience;

class DefaultController extends Controller
{
    public function aboutAction()
    {
        $repository = $this->getDoctrine()
            ->getEntityManager()
            ->getRepository('InertiaWinspireBundle:Page');
        $query = $repository->createQueryBuilder('p')
            ->where('p.slug = :slug')
            ->andWhere('p.section = :section')
            ->setParameter('slug', 'about')
            ->setParameter('section', 'about')
            ->getQuery();
        $page = $query->getSingleResult();
	    
        // no page match found
        try
        {
            $page = $query->getSingleResult();
        }
        catch(\Exception $e)
        {
            throw $this->createNotFoundException('That page does not exist');
        }
        
        $repository = $this->getDoctrine()
            ->getEntityManager()
            ->getRepository('InertiaWinspireBundle:Experience');
        $query = $repository->createQueryBuilder('e')
            ->where('e.is_featured = true')
            ->getQuery();
        $featured = $query->getResult();
        
        $repository = $this->getDoctrine()
            ->getEntityManager()
            ->getRepository('InertiaWinspireBundle:Category');
        $query = $repository->createQueryBuilder('c')
            ->orderBy('c.name', 'ASC')
            ->getQuery();
        $categories = $query->getResult();
        
        return $this->render('InertiaWinspireBundle:Default:about.html.twig',
            array(
                'page' => $page,
                'featured' => $featured,
                'categories' => $categories,
            )
        );
    }
    
    public function calendarAction()
    {
        $repository = $this->getDoctrine()
            ->getEntityManager()
            ->getRepository('InertiaWinspireBundle:Category');
        
        // grab the list of all categories
        $query = $repository->createQueryBuilder('c')
            ->where('c.is_active = true')
            ->orderBy('c.name', 'ASC')
            ->getQuery();
        $categories = $query->getResult();
        
    	// grab the list of "featured" Experiences
        $repository = $this->getDoctrine()
            ->getEntityManager()
            ->getRepository('InertiaWinspireBundle:Experience');
        $query = $repository->createQueryBuilder('e')
            ->where('e.is_featured = true')
            ->andWhere('e.is_active = true')
            ->getQuery();
        $featured = $query->getResult();
        
        // grab all experiences between now and 365 days
        $query = $repository->createQueryBuilder('e')
            ->where('e.start_date >= :start')
            ->andWhere('e.start_date < :end')
            ->andWhere('e.is_active = true')
            ->setParameter('start', new \DateTime())
            ->setParameter('end', new \DateTime('+1 year'))
            ->orderBy('e.start_date', 'ASC')
            ->getQuery();
        $experiences = $query->getResult();
        $total = count($experiences);
        
        // create a set of date "buckets" based on month,
        // and add each Experience to its appropriate bucket.
        $start = new \DateTime();
        $interval = new \DateInterval('P1M'); // 1 month
        $period = new \DatePeriod($start, $interval, 12);
        
        $temp = array();
        foreach($period as $dt)
        {
            $temp[$dt->format('Ym')] = array();
        }
        foreach($experiences as $experience)
        {
            $temp[$experience->getStartDate()->format('Ym')][] = $experience;
        }
        $experiences = $temp;
        
        return $this->render('InertiaWinspireBundle:Default:calendar.html.twig',
            array(
                'experiences' => $experiences,
                'featured' => $featured,
                'categories' => $categories,
                'date1' => new \DateTime(),
                'date2' => new \DateTime('+1 year'),
                'total' => $total
            )
        );
    }
    
    public function calendarDataAction(Request $request)
    {
        $categories = explode(',', $request->get('categories'));
        $start = $request->get('start');
        $end = $request->get('end');
        
        $qb = $this->getDoctrine()->getEntityManager()->createQueryBuilder();
        $qb->add('select', 'e');
        $qb->add('from', 'InertiaWinspireBundle:Experience e');
        $qb->leftJoin('e.category', 'c');
        $qb->orderBy('e.start_date', 'ASC');
        $qb->andWhere('e.start_date >= :start');
        $qb->andWhere('e.start_date <= :end');
        $qb->andWhere('e.is_active = true');
        if($start != '')
        {
        	// we have to subtract one second from the start date/time
        	$start = new \DateTime($start);
        	$start->sub(new \DateInterval('PT1S'));
        	
            $qb->setParameter('start', $start);
        }
        else
        {
            $qb->setParameter('start', new \DateTime());
        }
        if($end != '')
        {
            $qb->setParameter('end', new \DateTime($end));
        }
        else
        {
            $qb->setParameter('end', new \DateTime('+1 year'));
        }
        
        if(count($categories) > 0 && $categories[0] != '')
        {
            $qb->andWhere('c.slug in (?1)');
            $qb->setParameter(1, $categories);
        }
        $query = $qb->getQuery();
        
        $experiences = $query->getResult();
        $total = count($experiences);
        
        
        // create a set of date "buckets" based on month,
        // and add each Experience to its appropriate bucket.
        $start = new \DateTime();
        $interval = new \DateInterval('P1M'); // 1 month
        $period = new \DatePeriod($start, $interval, 12);
        
        $temp = array();
        foreach($period as $dt)
        {
            $temp[$dt->format('Ym')] = array();
        }
        foreach($experiences as $experience)
        {
            $temp[$experience->getStartDate()->format('Ym')][] = $experience;
        }
        $experiences = $temp;
        
        return $this->render('InertiaWinspireBundle:Default:calendarData.html.twig',
            array(
                'experiences' => $experiences,
                'total' => $total
            )
        );
    }
    
    public function categoryAction($slug)
    {
    	$collectionConstraint = new Collection(array(
    			'first_name' => array(new NotBlank(array('message' => 'First name is required'))),
    			'last_name' => array(new NotBlank(array('message' => 'Last name is required'))),
    			'company' => array(new NotBlank(array('message' => 'Company name is required'))),
    			'phone' => array(
    					new MinLength(array('limit' => 7, 'message' => 'Invalid phone number')),
    					new NotBlank(array('message' => 'Phone number is required')),
    			),
    			'email' => array(
    					new Email(array('message' => 'Invalid email address')),
    					new NotBlank(array('message' => 'Email address is required')),
    			),
    			'size' => array(),
    			'message' => array(),
    			'experience' => array(),
    	));
    	
    	$form = $this->createFormBuilder(null, array(
    			'validation_constraint' => $collectionConstraint,
    	))
    	->add('first_name', 'text')
    	->add('last_name', 'text')
    	->add('company', 'text')
    	->add('phone', 'text')
    	->add('email', 'text')
    	->add('size', 'text', array('required' => false, 'label' => 'Size of Group'))
    	->add('message', 'textarea', array('required' => false, 'label' => 'Enter a message...'))
    	->add('experience', 'hidden')
    	->getForm();
    	
    	
    	
        $repository = $this->getDoctrine()
            ->getEntityManager()
            ->getRepository('InertiaWinspireBundle:Category');
        
        // grab the list of all categories
        $query = $repository->createQueryBuilder('c')
            ->where('c.is_active = true')
            ->orderBy('c.name', 'ASC')
            ->getQuery();
        $categories = $query->getResult();
        
        $category = null;
        foreach($categories as $item)
        {
            if($item->getSlug() == $slug)
            {
                $category = $item;
            }
        }
        
        // no category match found
        if(!$category)
        {
            throw $this->createNotFoundException('That category does not exist');
        }

         
        // grab all Experiences in the same category
        $repository = $this->getDoctrine()
            ->getEntityManager()
            ->getRepository('InertiaWinspireBundle:Experience');
        $query = $repository->createQueryBuilder('e')
            ->where('e.category_id = :id')
            ->andWhere('e.is_active = true')
            ->setParameter('id', $category->getId())
            ->orderBy('e.name', 'ASC')
            ->getQuery();
        $experiences = $query->getResult();
        
        
        // grab the list of "featured" Experiences
        $query = $repository->createQueryBuilder('e')
            ->where('e.is_featured = true')
            ->andWhere('e.is_active = true')
            ->getQuery();
        $featured = $query->getResult();
        
        return $this->render('InertiaWinspireBundle:Default:category.html.twig',
            array(
                'experiences' => $experiences,
                'featured' => $featured,
                'form' => $form->createView(),
                'categories' => $categories,
                'category' => $category
            )
        );
    }
    
    public function ajaxContactAction(Request $request)
    {
        $collectionConstraint = new Collection(array(
            'first_name' => array(new NotBlank(array('message' => 'First name is required'))),
            'last_name' => array(new NotBlank(array('message' => 'Last name is required'))),
            'company' => array(new NotBlank(array('message' => 'Company name is required'))),
            'phone' => array(
                new MinLength(array('limit' => 7, 'message' => 'Invalid phone number')),
                new NotBlank(array('message' => 'Phone number is required')),
            ),
            'email' => array(
                new Email(array('message' => 'Invalid email address')),
                new NotBlank(array('message' => 'Email address is required')),
            ),
            'size' => array(),
            'message' => array(),
            'experience' => array(),
        ));
         
        $form = $this->createFormBuilder(null, array(
            'validation_constraint' => $collectionConstraint,
        ))
            ->add('first_name', 'text')
            ->add('last_name', 'text')
            ->add('company', 'text')
            ->add('phone', 'text')
            ->add('email', 'text')
            ->add('size', 'text', array('required' => false, 'label' => 'Size of Group'))
            ->add('message', 'textarea', array('required' => false, 'label' => 'Enter a message...'))
            ->add('experience', 'hidden')
            ->getForm();
        
        if($request->getMethod() == 'POST')
        {
            $form->bindRequest($request);
            
            if ($form->isValid())
            {
                $data = $form->getData();
    
    			// email the individual
    			$message = \Swift_Message::newInstance()
    			->setSubject('Thank you for contacting Winspire Sports & Entertainment')
    			->setFrom('info@wsenow.com')
    			->setTo($data['email'])
    			->setContentType('text/html')
    			->setBody($this->renderView('InertiaWinspireBundle:Default:contact1.html.twig', array(
    					'first_name' => $data['first_name'],
    					'last_name' => $data['last_name'],
    			), 'text/html')
    			);
    			$this->get('mailer')->send($message);
    
    			// email the Winspire team
    			$message = \Swift_Message::newInstance()
    			->setSubject('Contact has been received from wsenow.com')
    			->setFrom($data['email'])
    			->setTo('info@wsenow.com')
    			->setBody($this->renderView('InertiaWinspireBundle:Default:contact2.txt.twig', array(
    					'first_name' => $data['first_name'],
    					'last_name' => $data['last_name'],
    					'company' => $data['company'],
    					'phone' => $data['phone'],
    					'email' => $data['email'],
    					'size' => $data['size'],
    					'message' => $data['message'],
    					'experience' => $data['experience'],
    			)));
    			$this->get('mailer')->send($message);
    
    			// email (testing purposes)
    			$message = \Swift_Message::newInstance()
    			->setSubject('Contact has been received from wsenow.com')
    			->setFrom($data['email'])
    			->setTo('test@inertiaim.com')
    			->setBody($this->renderView('InertiaWinspireBundle:Default:contact2.txt.twig', array(
    					'first_name' => $data['first_name'],
    					'last_name' => $data['last_name'],
    					'company' => $data['company'],
    					'phone' => $data['phone'],
    					'email' => $data['email'],
    					'size' => $data['size'],
    					'message' => $data['message'],
    					'experience' => $data['experience'],
    			)));
    			$this->get('mailer')->send($message);
    
    			$this->get('session')->setFlash('notice', 'Thank you for contacting us!');
    		}
    	}
        
        return $this->render('InertiaWinspireBundle:Default:ajaxContact.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    public function contactAction(Request $request)
    {
        $collectionConstraint = new Collection(array(
            'first_name' => array(new NotBlank(array('message' => 'First name is required'))),
            'last_name' => array(new NotBlank(array('message' => 'Last name is required'))),
            'company' => array(new NotBlank(array('message' => 'Company name is required'))),
            'phone' => array(
                new MinLength(array('limit' => 7, 'message' => 'Invalid phone number')),
                new NotBlank(array('message' => 'Phone number is required')),
            ),
            'email' => array(
                new Email(array('message' => 'Invalid email address')),
                new NotBlank(array('message' => 'Email address is required')),
            ),
            'size' => array(),
            'message' => array(),
            'experience' => array(),
        ));
    	
        $form = $this->createFormBuilder(null, array(
            'validation_constraint' => $collectionConstraint,
        ))
            ->add('first_name', 'text')
            ->add('last_name', 'text')
            ->add('company', 'text')
            ->add('phone', 'text')
            ->add('email', 'text')
            ->add('size', 'text', array('required' => false, 'label' => 'Size of Group'))
            ->add('message', 'textarea', array('required' => false, 'label' => 'Enter a message...'))
            ->add('experience', 'hidden')
            ->getForm();
        
        if($request->getMethod() == 'POST')
        {
            $form->bindRequest($request);
            
            if ($form->isValid())
            {
                $data = $form->getData();
                
                // email the individual
                $message = \Swift_Message::newInstance()
                    ->setSubject('Thank you for contacting Winspire Sports & Entertainment')
                    ->setFrom('info@wsenow.com')
                    ->setTo($data['email'])
                    ->setContentType('text/html')
                    ->setBody($this->renderView('InertiaWinspireBundle:Default:contact1.html.twig', array(
                        'first_name' => $data['first_name'],
                        'last_name' => $data['last_name'],
                    ), 'text/html')
                );
                $this->get('mailer')->send($message);
                
                // email the Winspire team
                $message = \Swift_Message::newInstance()
                    ->setSubject('Contact has been received from wsenow.com')
                    ->setFrom($data['email'])
                    ->setTo('info@wsenow.com')
                    ->setBody($this->renderView('InertiaWinspireBundle:Default:contact2.txt.twig', array(
                        'first_name' => $data['first_name'],
                        'last_name' => $data['last_name'],
                        'company' => $data['company'],
                        'phone' => $data['phone'],
                        'email' => $data['email'],
                        'size' => $data['size'],
                        'message' => $data['message'],
                        'experience' => $data['experience'],
                    )));
                $this->get('mailer')->send($message);
                
                
                $this->get('session')->setFlash('notice', 'Thank you for contacting us!');
                return $this->redirect($this->generateUrl('contact'));
            }
        }
        
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
        
        return $this->render('InertiaWinspireBundle:Default:contact.html.twig', array(
            'ajax' => $request->isXmlHttpRequest(),
            'form' => $form->createView(),
            'featured' => $featured,
            'categories' => $categories,
        ));
    }
    
    public function detailAction($slug)
    {
        $repository = $this->getDoctrine()
            ->getEntityManager()
            ->getRepository('InertiaWinspireBundle:Experience');
        
        $query = $repository->createQueryBuilder('e')
            ->where('e.slug = :slug')
            ->andWhere('e.is_active = true')
            ->setParameter('slug', $slug)
            ->getQuery();
        
        $experience = $query->getSingleResult();
        
        return $this->render('InertiaWinspireBundle:Default:detail.html.twig',
            array(
                'experience' => $experience
            )
        );
    }
    
    public function indexAction()
    {
        $repository = $this->getDoctrine()
            ->getEntityManager()
            ->getRepository('InertiaWinspireBundle:Experience');
        
        $query = $repository->createQueryBuilder('e')
            ->where('e.is_home = true')
            ->andWhere('e.is_active = true')
            ->getQuery();
        
        $banners = $query->getResult();
        shuffle($banners);
        
        $query = $repository->createQueryBuilder('e')
            ->where('e.is_featured = true')
            ->andWhere('e.is_active = true')
            ->getQuery();
        
        $featured = $query->getResult();
        
        return $this->render('InertiaWinspireBundle:Default:index.html.twig',
            array(
                'banners' => $banners,
                'featured' => $featured
            )
        );
    }
    
    public function overviewAction()
    {
    	$collectionConstraint = new Collection(array(
    			'first_name' => array(new NotBlank(array('message' => 'First name is required'))),
    			'last_name' => array(new NotBlank(array('message' => 'Last name is required'))),
    			'company' => array(new NotBlank(array('message' => 'Company name is required'))),
    			'phone' => array(
    					new MinLength(array('limit' => 7, 'message' => 'Invalid phone number')),
    					new NotBlank(array('message' => 'Phone number is required')),
    			),
    			'email' => array(
    					new Email(array('message' => 'Invalid email address')),
    					new NotBlank(array('message' => 'Email address is required')),
    			),
    			'size' => array(),
    			'message' => array(),
    			'experience' => array(),
    	));
    	 
    	$form = $this->createFormBuilder(null, array(
    			'validation_constraint' => $collectionConstraint,
    	))
    	->add('first_name', 'text')
    	->add('last_name', 'text')
    	->add('company', 'text')
    	->add('phone', 'text')
    	->add('email', 'text')
    	->add('size', 'text', array('required' => false, 'label' => 'Size of Group'))
    	->add('message', 'textarea', array('required' => false, 'label' => 'Enter a message...'))
    	->add('experience', 'hidden')
    	->getForm();
    	 
    	
    	
    	
        $repository = $this->getDoctrine()
            ->getEntityManager()
            ->getRepository('InertiaWinspireBundle:Experience');
        
        $query = $repository->createQueryBuilder('e')
            ->where('e.is_active = true')
            ->orderBy('e.name')
            ->getQuery();
        $experiences = $query->getResult();
        $random = $experiences;
        shuffle($random);
        
        $query = $repository->createQueryBuilder('e')
            ->where('e.is_featured = true')
            ->andWhere('e.is_active = true')
            ->getQuery();
        $featured = $query->getResult();
        
        $repository = $this->getDoctrine()
            ->getEntityManager()
            ->getRepository('InertiaWinspireBundle:Category');
        
        // grab the list of all categories
        $query = $repository->createQueryBuilder('c')
            ->where('c.is_active = true')
            ->orderBy('c.name', 'ASC')
            ->getQuery();
        $categories = $query->getResult();
        
        return $this->render('InertiaWinspireBundle:Default:overview.html.twig',
            array(
                'experiences' => $experiences,
                'featured' => $featured,
                'form' => $form->createView(),
                'categories' => $categories,
                'random' => $random,
            )
        );
    }
    
    public function miniAction($slug)
    {
        $repository = $this->getDoctrine()
            ->getEntityManager()
            ->getRepository('InertiaWinspireBundle:Experience');
        
        $query = $repository->createQueryBuilder('e')
            ->where('e.slug = :slug')
            ->andWhere('e.is_active = true')
            ->setParameter('slug', $slug)
            ->getQuery();
        
        $experience = $query->getSingleResult();
        
        return $this->render('InertiaWinspireBundle:Default:mini.html.twig',
            array(
                'experience' => $experience
            )
        );
    }
    
    public function similarAction($slug)
    {
        $repository = $this->getDoctrine()
            ->getEntityManager()
            ->getRepository('InertiaWinspireBundle:Experience');
        
        $query = $repository->createQueryBuilder('e')
            ->where('e.slug = :slug')
            ->andWhere('e.is_active = true')
            ->setParameter('slug', $slug)
            ->getQuery();
        
        $experience = $query->getSingleResult();
        
        $query = $repository->createQueryBuilder('e')
            ->where('e.category_id = :id')
            ->setParameter('id', $experience->getCategoryId())
            ->andWhere('e.slug != :slug')
            ->andWhere('e.is_active = true')
            ->setParameter('slug', $slug)
            ->orderBy('e.name', 'ASC')
            ->getQuery();
        
        $similar = $query->getResult();
        
        return $this->render('InertiaWinspireBundle:Default:similar.html.twig',
            array(
                'similar' => $similar,
                'category' => $experience->getCategory()
            )
        );
    }
    
    public function staticAction($slug)
    {
        $repository = $this->getDoctrine()
            ->getEntityManager()
            ->getRepository('InertiaWinspireBundle:Page');
        $query = $repository->createQueryBuilder('p')
            ->where('p.slug = :slug')
            ->andWhere('p.is_active = true')
            ->andWhere('p.section is null')
            ->setParameter('slug', $slug)
            ->getQuery();

        // no page match found
        try
        {
            $page = $query->getSingleResult();
        }
        catch(\Exception $e)
        {
            throw $this->createNotFoundException('That page does not exist');
        }
        
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
        
        return $this->render('InertiaWinspireBundle:Default:static.html.twig',
            array(
                'page' => $page,
                'featured' => $featured,
                'categories' => $categories,
            )
        );
    }
    
    public function subscribeAction(Request $request)
    {
        $mailChimp = $this->get('MailChimp');
        $list = $mailChimp->getList();

//        $list->setMerge($array);  //optional default: null
//        $list->setEmailType('html'); //optional default: html
//        $list->setDoubleOption(true);  //optional default : true
//        $list->setUpdateExisting(false); // optional default : false
//        $list->setReplaceInterests(true);  // optional default : true
//        $list->SendWelcome(false);  // optional default : false

        $result = $list->Subscribe($request->get('email'));
        
        $response = new Response(json_encode(array('email' => $request->get('email'), 'result' => $result)));
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }
    
    public function whatAction($slug)
    {
    	$repository = $this->getDoctrine()
            ->getEntityManager()
            ->getRepository('InertiaWinspireBundle:Page');
        
        // If we get the default slug, we'll look for the first element
        // in the section and automatically redirect the visitor to that
        // specific page.
        if($slug == 'none')
        {
            $query = $repository->createQueryBuilder('p')
                ->andWhere('p.section = :section')
                ->andWhere('p.is_active = true')
                ->setParameter('section', 'what-we-do')
                ->orderBy('p.sort', 'ASC')
                ->setMaxResults(1)
                ->getQuery();
    		$page = $query->getSingleResult();
            
            return $this->redirect($this->generateUrl('what_we_do', array('slug' => $page->getSlug())), 301);
    	}
    	

        $query = $repository->createQueryBuilder('p')
            ->where('p.slug = :slug')
            ->andWhere('p.section = :section')
            ->andWhere('p.is_active = true')
            ->setParameter('slug', $slug)
            ->setParameter('section', 'what-we-do')
            ->getQuery();
        $page = $query->getSingleResult();
        
        // no page match found
        try
        {
            $page = $query->getSingleResult();
        }
        catch(\Exception $e)
        {
            throw $this->createNotFoundException('That page does not exist');
        }
        
        $query = $repository->createQueryBuilder('p')
            ->andWhere('p.section = :section')
            ->andWhere('p.is_active = true')
            ->setParameter('section', 'what-we-do')
            ->orderBy('p.sort', 'ASC')
            ->getQuery();
        $pages = $query->getResult();
        
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
        
        return $this->render('InertiaWinspireBundle:Default:what.html.twig',
            array(
                'page' => $page,
                'pages' => $pages,
                'featured' => $featured,
                'categories' => $categories,
            )
        );
    }
}
