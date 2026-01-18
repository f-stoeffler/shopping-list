<?php

namespace App\Controller\Api;

use App\Entity\Item;
use App\Entity\ShoppingList;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/lists')]
final class ShoppingListController extends AbstractController
{
    #[Route('/{id}/items', methods: ["GET"])]
    public function show(ShoppingList $shoppingList, SerializerInterface $serializer): JsonResponse
    {
        $json = $serializer->serialize($shoppingList, 'json', [
            'groups' => ['shopping-list']
        ]);

        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/{list_id}/items/{item_id}', methods: ["GET"])]
    public function show_item(#[MapEntity(id: 'list_id')] ShoppingList $shoppingList, #[MapEntity(id: 'item_id')] Item $item, SerializerInterface $serializer): JsonResponse
    {
        $itemShoppingListId = $item->getShoppingList()->getId();
        $shoppingListId = $shoppingList->getId();
        if ($shoppingListId !== $itemShoppingListId) {
            return new JsonResponse(["error" => "There is no item with ID " + $itemShoppingListId + "in this list"], 404);
        }

        $json = $serializer->serialize($item, 'json', [
            'groups' => ['item']
        ]);

        return new JsonResponse($json, 200, [], true);
    }

    #[Route(methods: ["POST"])]
    public function new(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
    {
        $content = $request->getContent();
        $shoppingList = $serializer->deserialize($content, ShoppingList::class, "json");
        $errors = $validator->validate($shoppingList);

        if (count($errors) > 0) {
            $error_messages = [];
            foreach ($errors as $error) {
                $error_messages[$error->getPropertyPath()][] = $error->getMessage();
            }

            return $this->json(["errors" => $error_messages], 422);
        }

        $em->persist($shoppingList);
        $em->flush();

        $json = $serializer->serialize($shoppingList, 'json', [
            'groups' => ['shopping-list']
        ]);
        return $this->json($json, 201);
    }

    #[Route('/{id}/item', methods: ["POST"])]
    public function new_item(ShoppingList $shoppingList, Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
    {
        $content = $request->getContent();
        $item = $serializer->deserialize($content, Item::class, "json");
        $item->setShoppingList($shoppingList);
        $errors = $validator->validate($item);

        if (count($errors) > 0) {
            $error_messages = [];
            foreach ($errors as $error) {
                $error_messages[$error->getPropertyPath()][] = $error->getMessage();
            }

            return $this->json(["errors" => $error_messages], 422);
        }

        $em->persist($item);
        $em->flush();

        $json = $serializer->serialize($shoppingList, 'json', [
            'groups' => ['shopping-list']
        ]);
        return $this->json($json, 201);
    }

    #[Route('/{list_id}/items/{item_id}', methods: ["PUT"])]
    public function update_item(#[MapEntity(id: 'list_id')] ShoppingList $shoppingList, #[MapEntity(id: 'item_id')] Item $item, Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
    {
        $itemShoppingListId = $item->getShoppingList()->getId();
        $shoppingListId = $shoppingList->getId();
        if ($shoppingListId !== $itemShoppingListId) {
            return new JsonResponse(["error" => "There is no item with ID " + $itemShoppingListId + "in this list"], 404);
        }

        $content = $request->getContent();
        $item = $serializer->deserialize($content, Item::class, "json", ["object_to_populate" => $item]);
        $errors = $validator->validate($item);

        if (count($errors) > 0) {
            $error_messages = [];
            foreach ($errors as $error) {
                $error_messages[$error->getPropertyPath()][] = $error->getMessage();
            }

            return $this->json(["errors" => $error_messages], 422);
        }

        $em->flush();

        $json = $serializer->serialize($item, 'json', [
            'groups' => ['item']
        ]);
        return $this->json($json);
    }

    #[Route('/{id}', methods: ["DELETE"])]
    public function delete(ShoppingList $shoppingList, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($shoppingList);
        $em->flush();

        return $this->json(null, 204);
    }

    #[Route('/{list_id}/items/{item_id}', methods: ["DELETE"])]
    public function delete_item(#[MapEntity(id: 'list_id')] ShoppingList $shoppingList, #[MapEntity(id: 'item_id')] Item $item, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse
    {
        $itemShoppingListId = $item->getShoppingList()->getId();
        $shoppingListId = $shoppingList->getId();
        if ($shoppingListId !== $itemShoppingListId) {
            return new JsonResponse(["error" => "There is no item with ID " + $itemShoppingListId + "in this list"], 404);
        }

        $em->remove($item);
        $em->flush();

        $json = $serializer->serialize($shoppingList, 'json', [
            'groups' => ['shopping-list']
        ]);

        return $this->json($json, 200);
    }
}
