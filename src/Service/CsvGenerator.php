<?php

namespace App\Service;

use App\Service\LatLangAddress;

class CsvGenerator
{
	private $rootPath;
	private $appKernel;
	private $orders = array();
	private $headers = array();

	public function __construct(string $rootPath)
	{
		$this->rootPath = $rootPath;
	}

	public function set_headers(array $headers){
		$this->headers = $headers;
	}

	public function read_csv()
	{
		$url_path = "https://s3-ap-southeast-2.amazonaws.com/catch-code-challenge/challenge-1/orders.jsonl";
		
		$destination = $this->rootPath.'/var/downloads/';
		
		if(!file_exists($destination)){
			mkdir($destination);
		}
		
		$file_name = $destination. basename($url_path);

		if(file_put_contents($file_name, file_get_contents($url_path))){
			$this->orders = file($file_name, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		} 
	}

	protected function format_csv_data()
	{
		if(empty($this->orders)){
			$this->read_csv();
		}

		if(!empty($this->orders)){
			foreach($this->orders as $order){
				$decoded_order = json_decode($order);
				$shipping_address = implode(" ", (array)($decoded_order->customer->shipping_address));
				$latlang = LatLangAddress::get_lat_lang($shipping_address);
				$decoded_order->customer->shipping_address->latitude = $latlang['latitude'];
				$decoded_order->customer->shipping_address->longitude = $latlang['longitude'];
				yield $decoded_order;
			}	
		}
	}

	public function export_csv()
	{
		// $headers
		$formatted_data = $this->format_csv_data();
		$csv_data = array();

		if(empty($this->headers)){
			echo "headers are missing";
			return;
		}

		$csv_data[] = $this->headers;

		foreach($formatted_data as $data){
			
			if(!$data) continue;

			$csv_formatted_data = array();
			// dump($data); die();

			$total_order_value = $average_unit_price = 0.0;
			$total_units_count = 0;

			foreach($data->items as $item){
				$total_units_count += $item->quantity;
				$total_order_value += ($item->quantity * $item->unit_price);
			}


			if($data->discounts){

				if(count($data->discounts) > 1){
					$priority  = array_column($data->discounts, 'priority');

					//sort the discounts array based on priority value in ASC order
					// priority 1 will be implemented first
					// priority 2 will be implemented second
					// and so on
					array_multisort($priority, SORT_ASC,  $data->discounts);
				}

				foreach($data->discounts as $discount){
					if($discount->type === 'DOLLAR'){
						$total_order_value -= $discount->value;		
					}

					if($discount->type === 'PERCENTAGE'){
						$total_order_value -= ($total_order_value * ($discount->value/100));
					}
				}
				 
			} 
			
			$csv_formatted_data['order_id'] = $data->order_id;
 			$csv_formatted_data['order_datetime'] = $data->order_date;
 			$csv_formatted_data['total_order_value'] = round($total_order_value,2);
 			$csv_formatted_data['average_unit_price'] = round($total_order_value/$total_units_count, 2);
 			$csv_formatted_data['distinct_unit_count'] = count($data->items);
 			$csv_formatted_data['total_units_count'] = $total_units_count;
 			$csv_formatted_data['customer_state'] = $data->customer->shipping_address->state;
 			$csv_formatted_data['latitude'] = $data->customer->shipping_address->latitude;
			$csv_formatted_data['longitude'] = $data->customer->shipping_address->longitude;
 			
 			$csv_data[] = $csv_formatted_data;
						
		}


		$output_file = $this->rootPath.'/var/downloads/orders.csv';
		$fp = fopen($output_file, "w");
		foreach($csv_data as $data){
			fputcsv($fp, $data);
		}
		fclose($fp);

		echo "CSV has been generated in the following path var/downloads/orders.csv";
	}

}