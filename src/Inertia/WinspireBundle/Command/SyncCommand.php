<?php
namespace Inertia\WinspireBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncCommand extends ContainerAwareCommand
{
    private $soapClientBasedir;
    private $user;
    private $password;
    private $sfConnection;
    private $soapClient;
    private $uploadDir;
    
    public function __construct()
    {
    	$this->soapClientBasedir = __DIR__ . '/../../../../vendor/salesforce/soapclient';
    	$this->uploadDir = __DIR__ . '/../../../../web/uploads/assets';
       
	$this->user = 'iim@inertiaim.com';
$this->password = 'MammothLakes2015!pYbisNUKiMVv04WHcjLPWqKTg';
	require_once($this->soapClientBasedir . '/SforcePartnerClient.php');
        require_once($this->soapClientBasedir . '/SforceHeaderOptions.php');
        
        $this->sfConnection = new \SforcePartnerClient();
        $this->soapClient = $this->sfConnection->createConnection($this->soapClientBasedir . '/partner.wsdl.xml');
        
        $this->sfConnection->login($this->user, $this->password);
        
        parent::__construct();
    }
    
    protected function configure()
    {
        $this
            ->setName('winspire:sync')
            ->setDescription('Pull content updates from SF.com');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $categoryRepository = $em->getRepository('InertiaWinspireBundle:Category');
        $experienceRepository = $em->getRepository('InertiaWinspireBundle:Experience');
        $pageRepository = $em->getRepository('InertiaWinspireBundle:Page');
        $testimonialRepository = $em->getRepository('InertiaWinspireBundle:Testimonial');
        
        
        // Sync Categories marked as 'dirty'
        $query = $categoryRepository->createQueryBuilder('c')
            ->where('c.is_dirty = true')
            ->andWhere('c.sf_id IS NOT NULL')
            ->getQuery();
        
        $categories = $query->getResult();
        
        $sfIds = array();
        foreach($categories as $category)
        {
            $sfIds[] = $category->getSfId();
        }
        
        if(count($sfIds) > 0)
        {
            $output->writeln(sprintf('Polling SF for updated Categories...'));
            $records = $this->sfConnection->retrieve('Name, Slug__c', 'Category__c', $sfIds);
        }
        else
        {
            $records = array();
        }
        
        $retrievedIds = array();
        foreach($records as $record)
        {
        	$retrievedIds[] = $record->Id;
        }
        
        $categoryArray = array();
        foreach($categories as $category)
        {
            $key = array_search($category->getSfId(), $retrievedIds);
            if($key !== FALSE)
            {
                $output->writeln(sprintf('%s', $category->getSfId()));
                
                $category->setName($records[$key]->fields->Name);
                $category->setSlug($records[$key]->fields->Slug__c);
                $category->setIsDirty(false);
                $category->setIsActive(true);
                $em->flush();
                
                $categoryArray[$category->getSfId()] = $category;
            }
        }
        
        
        
        // Create array of possible Category objects
        $query = $categoryRepository->createQueryBuilder('c')
            ->where('c.is_active = true')
            ->andWhere('c.sf_id IS NOT NULL')
            ->getQuery();
        
        $categories = $query->getResult();
        
        $categoryArray = array();
        foreach($categories as $category)
        {
            $categoryArray[$category->getSfId()] = $category;
        }
        
        
        
        // Sync Experiences marked as 'dirty'
        $query = $experienceRepository->createQueryBuilder('e')
            ->where('e.is_dirty = true')
            ->andWhere('e.sf_id IS NOT NULL')
            ->getQuery();
    	
        $experiences = $query->getResult();
        
        $sfIds = array();
        foreach($experiences as $experience)
        {
            $sfIds[] = $experience->getSfId();
        }
        
        if(count($sfIds) > 0)
        {
            $output->writeln(sprintf('Polling SF for updated Experiences...'));
            $records = $this->sfConnection->retrieve('IsActive, Name, Category__c, Description2__c, Detail__c, End_Date__c, Featured__c, Home_Page__c, Location__c, Slug__c, Start_Date__c', 'Product2', $sfIds);
        }
        else
        {
            $records = array();
        }
        
        $retrievedIds = array();
        foreach($records as $record)
        {
            $retrievedIds[] = $record->Id;
        }
        
        foreach($experiences as $experience)
        {
            $key = array_search($experience->getSfId(), $retrievedIds);
            if($key !== FALSE)
            {
                $output->writeln(sprintf('%s', $experience->getSfId()));
                
                $experience->setName($records[$key]->fields->Name);
                $experience->setSlug($records[$key]->fields->Slug__c);
                $experience->setLocation($records[$key]->fields->Location__c);
                $experience->setIsFeatured($records[$key]->fields->Featured__c == 'true');
                $experience->setIsHome($records[$key]->fields->Home_Page__c == 'true');
                $experience->setIsActive($records[$key]->fields->IsActive == 'true');
                $experience->setDescription($records[$key]->fields->Description2__c);
                $experience->setDetail($records[$key]->fields->Detail__c);
                if($records[$key]->fields->Start_Date__c == '')
                {
                    $experience->setStartDate(null);
                }
                else
                {
                    $experience->setStartDate(new \DateTime($records[$key]->fields->Start_Date__c));
                }
                if($records[$key]->fields->End_Date__c == '')
                {
                    $experience->setEndDate(null);
                }
                else
                {
                    $experience->setEndDate(new \DateTime($records[$key]->fields->End_Date__c));
                }
                $experience->setCategory($categoryArray[$records[$key]->fields->Category__c]);
                $experience->setIsDirty(false);
                $experience->setImage(null);
                $experience->setMiniImage(null);
                
                // Retrieve associated SF Attachments
                $attachments = $this->sfConnection->query("SELECT Id, BodyLength, Name, ContentType FROM Attachment WHERE ParentId='" . $experience->getSfId() . "'");
                
                foreach($attachments->records as $attachment)
                {
                    if($attachment->fields->ContentType == 'image/jpeg')
                    {
                        if($attachment->fields->Name == $experience->getSlug() . '.jpg')
                        {
                            $body = $this->sfConnection->retrieve('Body', 'Attachment', $attachment->Id);
                            $experience->setImage($attachment->fields->Name);
                            $fp = fopen($this->uploadDir . '/experiences/images/' . $attachment->fields->Name , 'wb');
                            fwrite($fp, base64_decode($body[0]->fields->Body));
                            fclose($fp);
                        }
                        if($attachment->fields->Name == $experience->getSlug() . '-mini.jpg')
                        {
                            $body = $this->sfConnection->retrieve('Body', 'Attachment', $attachment->Id);
                        	$experience->setMiniImage($attachment->fields->Name);
                        	$fp = fopen($this->uploadDir . '/experiences/images/' . $attachment->fields->Name , 'wb');
                        	fwrite($fp, base64_decode($body[0]->fields->Body));
                        	fclose($fp);
                        }
                    }
                }
                
                $em->flush();
            }
        }
        
        
        
        // Sync Pages marked as 'dirty'
        $query = $pageRepository->createQueryBuilder('p')
            ->where('p.is_dirty = true')
            ->andWhere('p.sf_id IS NOT NULL')
            ->getQuery();
            
        $pages = $query->getResult();
        
        $sfIds = array();
        foreach($pages as $page)
        {
            $sfIds[] = $page->getSfId();
        }
        
        if(count($sfIds) > 0)
        {
            $output->writeln(sprintf('Polling SF for updated Pages...'));
            $records = $this->sfConnection->retrieve('Title__c, Slug__c, Section__c, Description__c, Content__c', 'Page__c', $sfIds);
        }
        else
        {
            $records = array();
        }
        
        $retrievedIds = array();
        foreach($records as $record)
        {
            $retrievedIds[] = $record->Id;
        }
        
        foreach($pages as $page)
        {
            $key = array_search($page->getSfId(), $retrievedIds);
            if($key !== FALSE)
            {
        	    $output->writeln(sprintf('%s', $page->getSfId()));
                
                $page->setTitle($records[$key]->fields->Title__c);
                $page->setSlug($records[$key]->fields->Slug__c);
                $page->setDescription($records[$key]->fields->Description__c);
                $page->setContent($records[$key]->fields->Content__c);
                $page->setIsDirty(false);
                $page->setImage(null);
                
                // Retrieve associated SF Attachments
                $attachments = $this->sfConnection->query("SELECT Id, BodyLength, Name, ContentType FROM Attachment WHERE ParentId='" . $page->getSfId() . "'");
                
        		foreach($attachments->records as $attachment)
                {
                    $body = $this->sfConnection->retrieve('Body', 'Attachment', $attachment->Id);
                    $page->setImage($attachment->fields->Name);
                    $fp = fopen($this->uploadDir . '/pages/' . $attachment->fields->Name , 'wb');
                    fwrite($fp, base64_decode($body[0]->fields->Body));
                    fclose($fp);
                    break;
                }
                
                $em->flush();
            }
        }
        
        
        
        // Sync Testimonials marked as 'dirty'
        $query = $testimonialRepository->createQueryBuilder('t')
            ->where('t.is_dirty = true')
            ->andWhere('t.sf_id IS NOT NULL')
            ->getQuery();
        
        $testimonials = $query->getResult();
        
        $sfIds = array();
        foreach($testimonials as $testimonial)
        {
            $sfIds[] = $testimonial->getSfId();
        }
        
        if(count($sfIds) > 0)
        {
            $output->writeln(sprintf('Polling SF for updated Testimonials...'));
            $records = $this->sfConnection->retrieve('Name, Title__c, Company__c, Quote__c, Active__c', 'Testimonial__c', $sfIds);
        }
        else
        {
            $records = array();
        }
        
        $retrievedIds = array();
        foreach($records as $record)
        {
            $retrievedIds[] = $record->Id;
        }
        
        foreach($testimonials as $testimonial)
        {
            $key = array_search($testimonial->getSfId(), $retrievedIds);
            if($key !== FALSE)
            {
                $output->writeln(sprintf('%s', $testimonial->getSfId()));
                
                $testimonial->setName($records[$key]->fields->Name);
                $testimonial->setTitle($records[$key]->fields->Title__c);
                $testimonial->setCompany($records[$key]->fields->Company__c);
                $testimonial->setQuote($records[$key]->fields->Quote__c);
                $testimonial->setIsDirty(false);
                $testimonial->setIsActive($records[$key]->fields->Active__c == true);
                
                $em->flush();
            }
        }
    }
}
