<?php	
	// Set headers.
	header ("Content-Type: text/html; charset=utf-8");

	/**
     * Array of tests with it's functionality.
     * Has a structure like:
     *
     * @var array
     */
	$tests = array();

	// Attach executable test files and make $tests array.
	$files = scandir(getcwd().'/tests');
	array_shift($files);
	array_shift($files);
	if(!empty($files)) {
		foreach($files as $file) {
			require_once(getcwd() . '/tests/' . $file);

			$class = str_replace('.php', '', $file);
			$functionality = get_class_methods($class);	

			foreach($functionality as $func) {
				// Ignores magic methods.
				if(substr($func, 0, 2) !== '__')
					$tests[$class][substr($func, 0, -6 - strlen($class))][] = $func;
			}
		}
	}
	
	$passes = 0;
	$calls = 0;
	$total = 0;

	// Automaticly create all test objects and execute all test methods.
	if(!empty($tests)) {
		foreach ($tests as $test => $functionality) {
			try {
				// Dynamically creating test objects.
				$$test = new $test();

				// Define test controller.
				$$test->controller = $test;

				if(!$$test) 
					throw new Exception("Error Initializing {$test} Object", 1);

				// Dynamically calling test methods.	
				if(!empty($functionality)) {
					$total += count($functionality);

					foreach ($functionality as $generalMethodName => $functionPackage) {
						// Define test controller.
						$$test->function = $generalMethodName;

						// This call invokes __call() method that do everything we need.
						$reports[$generalMethodName . $test] = $$test->$generalMethodName();
						$calls++;
						$passes += ($reports[$generalMethodName . $test] == 'Passed') ?: 0;
					}
				}
			} catch (Exception $e) {
		    	echo '<b>Exception:</b> ' . $e->getMessage(), "\n";
			} finally {
				// Create report.
				$output = array(
					'Total' => $total,
					'Called' => $calls,
					'Passed' => $passes,
					'Failed' => $calls - $passes,
				);

				echo "<hr/>";

				foreach($output as $state => $count)
					echo "<div><b>{$state}:</b> {$count}</div>";

				echo "<hr/>";
				
				foreach($reports as $function => $report)
					echo "<div><b>{$function}</b> - {$report}</div>";
			}
		}
	}