<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Note;
use App\Entity\Category;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\NoteType;

class NoteController extends AbstractController
{
    /**
     * @Route("/", name="note_index")
     */
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $repNote = $doctrine->getRepository(Note::class);
        $repCategory = $doctrine->getRepository(Category::class);
        return $this->render('note/index.html.twig', [
        'categories' => $repCategory->findBy(array(), array('created_at' => 'DESC')),
        'notes' => $repNote->findBy(array(), array('created_at' => 'DESC')),
        
        'note_id' => $request->query->get('note_id')
      ]);
    }
    /**
     * @Route("/notes/category/{id}", name="note_category_index", requirements={"id"="\d+"})
     */
    public function index_cat(ManagerRegistry $doctrine, int $id)
    {
        $repCategory = $doctrine->getRepository(Category::class);
        $category = $repCategory->find($id);
        $notes = $category->getNotes();
        return $this->render('note/index.html.twig', [
        'categories' => $repCategory->findBy(array(), array('created_at' => 'DESC')),
        'notes' => $notes,
        'category_id' => $id
      ]);
    }

    /**
      * @Route("/note/{id}", name="show_note", requirements={"id"="\d+"})
    */
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
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
            return $this->redirectToRoute('note_index', ['note_id' => $note->getId()]);
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
            return $this->redirectToRoute('note_index', ['note_id' => $id]);
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
