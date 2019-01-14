<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Words extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'words:clean {inFile} {outFile}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean a word file list of any non alpha characters.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle() {
        $fp = fopen($this->argument('inFile'), 'r');
        $out = fopen($this->argument('outFile'), 'w');
        $cleaned = 0;
        while($line = fgets($fp, 64))
        {
            $test = trim($line);
            if (!preg_match("/^[A-Za-z]+$/", $test, $match)) {
                $cleaned++;
                continue;
            }
            // $withEnd = "$line\n";
            fwrite($out, $line, strlen($line));
        }
        fclose($out);
        fclose($fp);
        echo "Cleaned: $cleaned\n";
    }
}