<?php

namespace App\Controller;

use App\Entity\Data;
use App\Form\ProductType;
use App\Service\ExcelExportService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route(path: '/', name: 'product_page')]
    public function productPage(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException("Użytkownik nie jest zalogowany.");
        }

        $products = $em->getRepository(Data::class)
            ->findBy(['user' => $user]);
        //dd($products);
        return $this->render('product/product.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/product/new', name: 'product_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $product = new Data();
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setUser($user);
            $product->setDate(new \DateTime());
            $em->persist($product);
            $em->flush();

            return $this->json([
                'id' => $product->getId(),
                'date' => $product->getDate() ? $product->getDate()->format('Y-m-d H:i') : '',
                'amount' => $product->getAmount(),
                'color' => $product->getColor(),
                'product' => $product->getProduct(),
            ]);
        }

        return $this->render('product/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/products/export', name: 'products_export')]
    public function export(EntityManagerInterface $em, ExcelExportService $excelExport): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $products = $em->getRepository(Data::class)->findBy(['user' => $user]);

        $spreadsheet = $excelExport->generateProductsExcel($products);
        $writer = $excelExport->getWriter($spreadsheet);

        $response = new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="products.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}
