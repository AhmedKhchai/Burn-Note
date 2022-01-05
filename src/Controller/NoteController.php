<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Note;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\NoteType;

class NoteController extends AbstractController
{
    /**
     * @Route("/", name="note_index")
     */
    public function listNotes(ManagerRegistry $doctrine): Response
    {
      $rep = $doctrine->getRepository(Note::class);
      return $this->render('note/index.html.twig', [
        'notes' => $rep->findAll(),
      ]);
    }

    /**
      * @Route("/note/{id}", name="show_note", requirements={"id"="\d+"})
    */
    public function showNote(ManagerRegistry $doctrine, int $id): Response {
      $rep = $doctrine->getRepository(Note::class);
      $note = $rep->find($id);
      return $this->render('note/show_note.html.twig', [
        'note' => $note
      ]);
    }

    /**
     * @Route("/create", name="note_create")
     */
    public function create(ManagerRegistry $doctrine, Request $request): Response
    {
      $note = new Note();
      $form = $this->createForm(NoteType::class, $note);

      $form->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid()) {
        $em = $doctrine->getManager();
        $em->persist($note);
        $em->flush();
        return $this->redirectToRoute('note_index');
      }

      return $this->render('note/create.html.twig', [
        'form' => $form->createView()
      ]);
    }

    /**
     * @Route("/edit/{id}", name="note_edit", requirements={"id"="\d+"})
     */
    public function edit(ManagerRegistry $doctrine, Request $request, int $id): Response
    {
      $rep = $doctrine->getRepository(Note::class);
      $note = $rep->find($id);
      $form = $this->createForm(NoteType::class, $note);

      $form->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid()) {
        $em = $doctrine->getManager();
        $em->flush();
        return $this->redirectToRoute('note_index');
      }

      return $this->render('note/create.html.twig', [
        'form' => $form->createView()
      ]);
    }

    /**
     * @Route("/delete/{id}", name="note_delete", requirements={"id"="\d+"})
     */
    public function delete(ManagerRegistry $doctrine, Request $request, int $id): Response
    {
      $rep = $doctrine->getRepository(Note::class);
      $note = $rep->find($id);
      $em = $doctrine->getManager();
      $em->remove($note);
      $em->flush();

      // enlever la redirection et déclencher une animation qui brûle la note
      return $this->redirectToRoute('note_index');
    }
}
