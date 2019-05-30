<html>
	<head>
		<style>
			td{
				padding:10px;
			}
			table, td {
				border:1px solid black;
			}
		</style>
	</head>
</html>
<?php
	class BenchmarkApp {

		//set global properties to be initialized
		protected $functions;// array of callbacks to be analyzed
		protected $times_to_execute; // number of times to execute, must be an integer
		protected $metrics; // array of metrics to compare runtimes, acceptable values are 'average', 'longest', 'shortest', and 'difference', defined in the comparitor() method
		protected $order; //sort order for rankings, acceptables values are 'asc' and 'desc'
		protected $output_method; //output value for displaying results, acceptable values are 'web' and 'txt'

		//take user defined parameters and initialize values
		function __construct (
			array $functions, //array of callbacks to be analyzed
			int $times_to_execute = 2, //number of times to execute 
			array $metrics = ['average'], //array of metrics to compare runtimes
			string $order = 'asc', //sort rankings in ascending or descending order
			string $output_method = 'web' //output in browser or file
		) {
			//initialize values of global variables
			$this->functions = $functions;
 			$this->times_to_execute = $times_to_execute;
 			$this->metrics = $metrics;
 			$this->order = $order;
 			$this->output_method = $output_method;

 			// begin executing callback evaluation
    		$this->benchmark($functions, $times_to_execute);

		}

		//take array of callbacks and execute each a set number of times
		private function benchmark(array $functions, int $times_to_execute) {
			$execution_times = []; //prepare array to store callback sub-arrays
			foreach($functions as $function_call){
				$execution_times[$function_call] = []; //prepare callback sub-array to store function runtimes
			    for($i = 0; $i<$times_to_execute; $i++){
			        $execution_times[$function_call][$i] = $this->callIt($function_call); //run callback and store runtime value
			    }
			}

			// pass runtime value array to the comparator function for analysis with array of metrics and sort order
			$this->comparator($execution_times, $this->metrics, $this->order);
		}

		private function comparator(array $execution_times, array $metrics, string $order) {
			$comparisons = [];//prepare array to store analysis of data
			

			//analysis block. loops through runtime results, evaluating on each metric and then storing in callback subarray
			foreach($execution_times as $function_name => $function_times){
				foreach ($metrics as $metric) {
					switch ($metric) {
						case 'average': //average the runtimes. the avg function is defined later in this class
					        $value = $this->avg($function_times);
							break;
						case 'longest': //get the longest single runtime
					        $value = max($function_times);
							break;
						case 'shortest': //get the shortest single runtime
					        $value = min($function_times);
							break;
						case 'difference': //get the largest difference between runtimes (good for finding callbacks whose runtimes vary wildly)
					        $value = max($function_times) - min($function_times);
							break;
						default: //metric not defined, should throw an error
							echo "something went wrong";
							break;
					}
					$comparisons[$function_name][$metric] = $value;
				}

			}
			//rank the callbacks based on the average runtimes of all analyses provided. the avg method is defined later in this class
			switch ($order) {
				case 'asc':
					uasort($comparisons, function ($a, $b) {
					    return $this->avg($a) <=> $this->avg($b);
					});
					break;
				case 'desc':
					uasort($comparisons, function ($a, $b) {
					    return $this->avg($b) <=> $this->avg($a);
					});
					break;
				default:
					echo "something went wrong";
					break;
			}

			//begin the reporting process, passing in the analysis results as well as the preferred output method and metrics
			$this->reporter($comparisons, $this->output_method, $this->metrics) ;
		}

		private function reporter(array $report,  string $output_method, array $metrics) {
			
			switch ($output_method) {
				case 'web'://display in browser window, built in a table format
					echo "<table>";
					echo "<tr class=\"border\" >";
					echo "<td>";
					echo "</td>";
					foreach($metrics as $metric){
						echo "<td>";
						echo $metric;
						echo "</td>";

					}
					echo "</tr>";
					foreach($report as $key => $item){
						echo "<tr class=\"border\" >";
						echo "<td>";
						echo $key;
						echo "</td>";
						foreach ($item as $subitem) {
							echo "<td>";
							echo $subitem;
							echo "</td>";
						}
						echo "</tr>";


					}
					echo "</table>";

					break;
				
				case 'txt'://write to text file, built in print_r array format
					$my_file = 'report.txt';
					$handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file);
					fwrite($handle, print_r($report, true));
					echo "written to ".$my_file;
					break;
				
				default:// output method not defined, this should throw an error
					echo "something went wrong";
					break;
			}
		}

		// method that takes and runs a callback, returning the runtime in seconds
	    private function callIt(callable $callback) {
			$start = microtime(true);
	        $callback();
			$time_elapsed_secs = microtime(true) - $start;
			return $time_elapsed_secs;
	    }


	    // method that averages an array, was just too useful to not build
	    private function avg(array $arr){
	    	$return = array_sum($arr)/count($arr);
	    	return $return;
	    }
	    
	}

	//test functions. 
    function doStuff() {
        $x = "Hello World!\n";
    }    
    function doOtherStuff() {
        $x = "Hello World!\n Hello World!\n";
    }    
    function doMoreStuff() {
        $x = "Hello World!\n Hello World!\n Hello World! \n";
    }

    // test initialization
    $benchmark = new BenchmarkApp(
    	['doStuff', 'doOtherStuff', 'doMoreStuff'], 
    	200000, 
    	['average', 'longest', 'shortest', 'difference'], 
    	'asc',
    	'web'
    );

    
?>