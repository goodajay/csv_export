<?php
 
 namespace App\Command;

 use Symfony\Component\Console\Command\Command;
 use Symfony\Component\Console\Input\InputInterface;
 use Symfony\Component\Console\Output\OutputInterface;

 use App\Service\CsvGenerator;

 class ExportOrdersCsvCommand extends Command
 {
 	//Command name
 	protected static $defaultName = 'app:export-orders-csv';
 	protected $csv_generator;

 	public function __construct(CsvGenerator $csv_generator)
 	{
 		$this->csv_generator = $csv_generator;

 		parent::__construct();
 	}

 	protected function configure()
 	{
 		$this
 			//short description shown while running "php bon/console list"
 			->setDescription("Export the orders to csv format and saved it in the download directory of the app root")

 			//the full command description shown when running the command with --help option
 			->setHelp("This command allows you to export orders data read from url to csv format which will be downloaded to the download folder in the app root");
 	}

 	protected function execute(InputInterface $input, OutputInterface $output)
 	{
 		$output->writeln("Export CSV command is running ...");

 		//set headers for the csv
 		$this->csv_generator->set_headers([
 			'order_id',
 			'order_datetime',
 			'total_order_value',
 			'average_unit_price',
 			'distinct_unit_count',
 			'total_units_count',
 			'customer_state',
 			'latitude',
			'longitude'
 		]);
 		
 		$this->csv_generator->export_csv();
 		return 0;
 	}
 }