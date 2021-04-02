<?php

namespace App\Controller;

use App\Entity\Admin\Messages;
use App\Entity\Blog;
use App\Entity\Setting;
use App\Form\Admin\MessagesType;
use App\Repository\Admin\CommentRepository;
use App\Repository\BlogRepository;
use App\Repository\ImageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Bridge\Google\Smtp\GmailTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SettingRepository;
class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(SettingRepository $settingRepository, BlogRepository $blogRepository)
    {
        $setting=$settingRepository->findAll();
        $slider=$blogRepository->findBy([],[], 5);
        $blogs=$blogRepository->findBy([],[], 4);


        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'setting' => $setting,
            'slider' => $slider,
            'blogs' => $blogs,
        ]);
    }

    /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(Blog $blog, $id, ImageRepository $imageRepository, CommentRepository $commentRepository): Response
    {
        $images=$imageRepository->findBy(['blog'=>$id]);
        $comments=$commentRepository->findBy(['blogid'=>$id, 'status'=>'True']);

        return $this->render('home/blogshow.html.twig', [
            'blog' => $blog,
            'images' => $images,
            'comments' => $comments,
        ]);
    }

    /**
     * @Route("/about", name="home_about")
     */
    public function about(SettingRepository $settingRepository): Response
    {
        $setting=$settingRepository->findAll();
        return $this->render('home/aboutus.html.twig', [
            'setting' => $setting,
        ]);
    }

    /**
     * @Route("/contact", name="home_contact")
     */
    public function contact(SettingRepository $settingRepository, Request $request): Response
    {
        $message = new Messages();
        $form = $this->createForm(MessagesType::class, $message);
        $form->handleRequest($request);
        $submitedToken = $request->request->get('token');

        $setting=$settingRepository->findAll();

        if ($form->isSubmitted()) {
            if($this->isCsrfTokenValid('form-message', $submitedToken)) {
                $entityManager = $this->getDoctrine()->getManager();
                $message->setStatus('New');
                $message->setIp($_SERVER['REMOTE_ADDR']);
                $entityManager->persist($message);
                $entityManager->flush();
                $this->addFlash('success', 'Mesajınız Başarıyla Gönderilmiştir');
//**************************SEND MAİL***********************

                $email = (new Email())
                    ->from($setting[0]->getSmtpemail())
                    ->to($form['email']->getData())
                    ->subject('AllBlog Your Request')
                    ->html("Dear ". $form['name']->getData() ."<br>
                    <p>We will evluate your request and contact you as sonn as possible</p>
                    Thank you for your message<br>
                     *****************************************
                     <br>".$setting[0]->getDevelopment()." <br>
                     Adress  : ".$setting[0]->getAddress()." <br>
                     Phone   : ".$setting[0]->getPhone()."<br>"
);
               $transport = new GmailTransport($setting[0]->getSmtpemail(), $setting[0]->getSmtppassword());
               $mailer = new Mailer($transport);
               $mailer->send($email);

//*************************************************************
                return $this->redirectToRoute('home_contact');
            }
        }

        return $this->render('home/contact.html.twig', [
            'setting' => $setting,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/bloghome", name="home_bloghome")
     */
    public function bloghome(SettingRepository $settingRepository, BlogRepository $blogRepository)
    {
        $setting=$settingRepository->findAll();
        $blogs=$blogRepository->findAll();

        return $this->render('home/bloghome.html.twig', [
            'controller_name' => 'HomeController',
            'setting' => $setting,
            'blogs' => $blogs,
        ]);
    }

}
