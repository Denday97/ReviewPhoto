<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Photo;
use App\Form\CommentType;
use App\Form\NewPhotoType;
// use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationRequest;
use App\Repository\PhotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

class PhotoController extends AbstractController
{
    #[Route('/', name: 'photo.list')]
    public function list(PhotoRepository $photoRepository): Response
    {
        // $photos = new Photo();
        // $photo->setTitle('Ma première photo');
        // $photo->setPostAt( new \DateTimeImmutable()); 
        // $photos = [];
        // $photos[] = $photo;
        $photos = $photoRepository->findAll();
        return $this->render('photo/list.html.twig', ['photos' => $photos]);


        // return $this->render('photo/index.html.twig', [
        //     'controller_name' => 'PhotoController',
        // ]);
    }
    #[Route('/photo/show/{id}', name: 'photo.show')]
    public function show(Photo $photo, EntityManagerInterface $em, HttpFoundationRequest $request)
    {
        if ($this->getUser()) {
            $user = $this->getUser();

            $comment = new \App\Entity\Comment();
            $comment->setUser($user);
            $comment->setPhoto($photo);
            $comment->setCreateAt(new \DateTimeImmutable());

            $form = $this->createForm(CommentType::class, $comment);
            $form->handleRequest($request);
            if($form->isSubmitted()&& $form->isValid()) {
                $em->persist($comment);
                $em->flush();

                return $this->redirectToRoute('photo.show',['id' => $photo->getId()]);
            }else {
                return $this->render('photo/show.html.twig', [
                    'photo' => $photo,
                    'form' => $form->createView(),
                ]);
            } 

        } else {
            return $this->render('photo/show.html.twig',[
                'photo'=>$photo,
            ]);
        }
    }

    #[Route('/photo/manage', name: 'photo.manage')]
    public function manage()
    {
        $user = $this->getUser();
        $photos = $user->getPhotos();
        return $this->render('photo/manage.html.twig', ['photos' => $photos]);
    }

    #[Route('/photo/delete/{id}', name: 'photo.delete')]
    public function delete(PhotoRepository $repository, HttpFoundationRequest $request, EntityManagerInterface $manager)
    {
        $photo = $repository->find($request->get('id'));

        // $manager = $this->getDoctrine()->getManager();  
        $manager->remove($photo);
        $manager->flush();
        return $this->redirectToRoute('photo.manage');
    }
    
    #[Route('/photo/delete/{id}', name: 'photo.delete')]
    public function deleteComment(PhotoRepository $repository, HttpFoundationRequest $request, EntityManagerInterface $manager)
    {
        $photo = $repository->find($request->get('id'));

        // $manager = $this->getDoctrine()->getManager();  
        $manager->remove($photo);
        $manager->flush();
        return $this->redirectToRoute('photo.manage');
    }
    #[Route('/photo/new', name: 'photo.new')]
    public function new(HttpFoundationRequest $request, EntityManagerInterface $manager)
    {
        $photo = new Photo();
        $form = $this->createForm(NewPhotoType::class, $photo);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $photo->setUser($this->getUser());
            $photo->setPostAt(new \DateTimeImmutable());
            $manager->persist($photo);
            $manager->flush();
            return $this->redirectToRoute('photo.list');
        }
        return $this->render('photo/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
