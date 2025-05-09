<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/comment')]
class CommentController extends AbstractController
{
    #[Route('/{id}/delete', name: 'app_comment_delete', methods: ['POST'])]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $post = $comment->getPost();
        $workGroup = $post->getWorkGroup();

        if ($user !== $comment->getAuthor() && (!$workGroup || !$workGroup->getModerators()->contains($user))) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
            $em->remove($comment);
            $em->flush();
            $this->addFlash('success', 'Commentaire supprimé.');
        }

        return $this->redirectToRoute('app_post_show', ['id' => $post->getId()]);
    }

    #[Route('/new/{post}', name: 'app_comment_new', methods: ['POST'])]
    public function new(Request $request, Post $post, EntityManagerInterface $em): Response
    {
        $comment = new Comment();
        $comment->setPost($post);
        $comment->setAuthor($this->getUser());
        $comment->setCreatedAt(new \DateTimeImmutable());

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Commentaire ajouté.');
        }

        return $this->redirectToRoute('app_post_show', ['id' => $post->getId()]);
    }
}
