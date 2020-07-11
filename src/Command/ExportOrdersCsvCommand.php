<?php
 
 namespace App\Command;

 use Symfony\Component\Console\Command\Command;
 
 use Symfony\Component\Console\Input\InputInterface;
 use Symfony\Component\Console\Input\InputArgument;

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
 			->setHelp("This command allows you to export orders data read from url to csv format which will be downloaded to the download folder in the app root")

	 	   // configure an argument
	        ->addArgument('latlang', InputArgument::OPTIONAL, 'If true include latitude and langitude, false skip latitude and langitude');
 	}

 	protected function execute(InputInterface $input, OutputInterface $output)
 	{
 		$output->writeln("Export CSV command is running ...");

 		$headers = [
			'order_id',
 			'order_datetime',
 			'total_order_value',
 			'average_unit_price',
 			'distinct_unit_count',
 			'total_units_count',
 			'customer_state',
 		];

 		$latlang = $input->getArgument('latlang');

 		if(is_null($latlang)){
 			$latlang = 'false';
 		}

 		if($latlang != 'true'){
 			$output->writeln("Skipping Latitude and longitude values for the address\n");
 		} else {
 			$output->writeln("Latitude and longitude values has been added to the csv\n");
 		}

 		//set headers for the csv
 		$this->csv_generator->set_headers($headers, $latlang);
 		
 		$this->csv_generator->export_csv();
 		$output->writeln("Export CSV command has been executed successfully");
 		return 0;
 	}
 }