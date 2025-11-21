<?php

namespace App\Command;

use App\Entity\Data;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:fill-data',
    description: 'Import produktów z pliku XLSX',
)]
class FillDataCommand extends Command
{
    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em
    )
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Importuje produkty z pliku XLSX')
            ->addArgument('file', InputArgument::REQUIRED, 'Ścieżka do pliku XLSX');
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filePath = $input->getArgument('file');

        if ($filePath) {
            $io->note(sprintf('You passed an argument: %s', $filePath));
        }

        if (!file_exists($filePath)) {
            $io->note('Plik nie istnieje!');
            return Command::FAILURE;
        }

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Pomijamy nagłówki
        foreach (array_slice($rows, 1) as $row) {

            $id = $row[0];
            $userId = $row[1];
            $date = $row[2];
            $product = $row[3];
            $color = $row[4];
            $amount = (int)$row[5];

            $user = $this->em->getRepository(User::class)->find($userId);

            if (!$user) {
                $io->note(sprintf('Nie znaleziono user_id: %s — pomijam wiersz.', $userId));
                continue;
            }

            if (empty($userId) || empty($product)) {
                $io->note(sprintf('Pomijam, brakuje wymmaganej informacji dla utworzenia użytkownika. Id - %s', $id));
                continue;
            }

            $data = new Data();
            $data->setUser($user);
            $data->setDate(new DateTime($date));
            $data->setProduct($product);
            $data->setColor($color);
            $data->setAmount($amount);

            $this->em->persist($data);
        }

        $this->em->flush();

        $io->note('Import produktów zakończony pomyślnie.');

        return Command::SUCCESS;
    }
}
