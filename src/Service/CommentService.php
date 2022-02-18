<?php

namespace App\Service;

use App\Entity\Comments;
use App\Entity\Figures;
use Doctrine\ORM\EntityManagerInterface;


class CommentService
{
    private $entitymanager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entitymanager = $entityManager;

    }

    public function persistComment(Comments $comment, Figures $figure= null ): void
    {
        $comment->setFigure($figure)
            ->setCreatedAt(new \DateTimeImmutable('now'));

        $this->entitymanager->persist($comment);
        $this->entitymanager->flush();


    }

}

?>