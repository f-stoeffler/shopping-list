<?php

namespace App\Controller;

use App\Entity\Item;
use App\Entity\ShoppingList;
use App\Form\ItemType;
use App\Form\ShoppingListType;
use App\Repository\ShoppingListRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/lists')]
final class ShoppingListController extends AbstractController
{
    #[Route(name: 'app_shopping_list_index', methods: ['GET'])]
    public function index(ShoppingListRepository $shoppingListRepository): Response
    {
        return $this->render('shopping_list/index.html.twig', [
            'shopping_lists' => $shoppingListRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_shopping_list_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $shoppingList = new ShoppingList();
        $form = $this->createForm(ShoppingListType::class, $shoppingList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($shoppingList);
            $entityManager->flush();
            
        $this->addFlash(
            'primary',
            'Shopping list added!'
        );

            return $this->redirectToRoute('app_shopping_list_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('shopping_list/new.html.twig', [
            'shopping_list' => $shoppingList,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/items', name: 'app_shopping_list_items_edit', methods: ['GET'])]
    public function show(ShoppingList $shoppingList): Response
    {
        return $this->render('shopping_list/show.html.twig', [
            'shopping_list' => $shoppingList,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_shopping_list_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ShoppingList $shoppingList, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ShoppingListType::class, $shoppingList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash(
                'primary',
                'Shopping list updated!'
            );

            return $this->redirectToRoute('app_shopping_list_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('shopping_list/edit.html.twig', [
            'shopping_list' => $shoppingList,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_shopping_list_delete', methods: ['POST'])]
    public function delete(Request $request, ShoppingList $shoppingList, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $shoppingList->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($shoppingList);
            $entityManager->flush();
        }
        $this->addFlash(
            'primary',
            'Shopping List removed!'
        );

        return $this->redirectToRoute('app_shopping_list_index', [], Response::HTTP_SEE_OTHER);
    }



    #[Route('/{id}/new-item', name: 'app_shopping_list_item_new', methods: ['POST'])]
    public function new_item(Request $request, ValidatorInterface $validator, EntityManagerInterface $em, ShoppingList $shoppingList): Response
    {
        $item = new Item();
        $itemName = $request->getPayload()->getString('name');
        $item->setName($itemName);
        $item->setShoppingList($shoppingList);
        $errors = $validator->validate($item);

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->addFlash(
                    'danger',
                    $error->getMessage()
                );
            }

            return $this->redirectToRoute('app_shopping_list_items_edit', ['id' => $shoppingList->getId()], Response::HTTP_SEE_OTHER);
        }

        $em->persist($item);
        $em->flush();

        $this->addFlash(
            'primary',
            'Item added!'
        );

        return $this->redirectToRoute('app_shopping_list_items_edit', ['id' => $shoppingList->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/delete-item', name: 'app_shopping_list_item_delete', methods: ['POST'])]
    public function delete_item(Request $request, Item $item, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $item->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($item);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_shopping_list_index', [], Response::HTTP_SEE_OTHER);
    }
}
