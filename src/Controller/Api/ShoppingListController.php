<?php

namespace App\Controller\Api;

use App\Entity\ShoppingList;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/shopping-list')]
final class ShoppingListController extends AbstractController
{
    #[Route(name: 'app_api_shopping_list', methods: ["GET"])]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/Api/ShoppingListController.php',
        ]);
    }


    #[Route('/{id}/items', methods: ["GET"])]
    public function show(ShoppingList $shoppingList, SerializerInterface $serializer): JsonResponse
    {
        $json = $serializer->serialize($shoppingList, 'json', [
            'groups' => ['shopping_list:read']
        ]);

        return new JsonResponse($json, 200, [], true);
    }

    #[Route(methods: ["POST"])]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
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

        return $this->json($shoppingList, 201);
    }
}
