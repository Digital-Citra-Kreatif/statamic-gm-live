<?php

namespace App\Forms\Exporters;

use League\Csv\EscapeFormula;
use League\Csv\Writer;
use SplTempFileObject;
use Statamic\Facades\File;
use Statamic\Forms\Exporters\CsvExporter as StatamicCsvExporter;
use Statamic\Support\Arr;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use function Statamic\trans as __;

class CsvExporter extends StatamicCsvExporter
{
    private Writer $writer;

    public function export(): string
    {
        $this->writer = Writer::createFromFileObject(new SplTempFileObject);
        $this->writer->setDelimiter(Arr::get($this->config, 'delimiter', config('statamic.forms.csv_delimiter', ',')));
        $this->writer->addFormatter(new EscapeFormula("'"));

        $this->insertHeaders();
        $this->insertData();

        return $this->writer->toString();
    }

    public function contentType(): string
    {
        return 'text/csv; charset=UTF-8';
    }

    public function download(): BinaryFileResponse
    {
        // Bersihkan output buffer
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $content = $this->export();
        $path = storage_path('statamic/tmp/forms/'.$this->form->handle().'-'.time().'.'.$this->extension());

        File::put($path, $content);

        return response()
            ->download($path, $this->form->handle().'.'.$this->extension(), [
                'Content-Type' => $this->contentType(),
            ])
            ->deleteFileAfterSend();
    }

    private function insertHeaders(): void
    {
        $key = Arr::get($this->config, 'headers', config('statamic.forms.csv_headers', 'handle'));

        $headers = $this->form->fields()
            ->map(fn ($field) => $key === 'display' ? $field->display() : $field->handle())
            ->push($key === 'display' ? __('Date') : 'date')
            ->values()
            ->all();

        $this->writer->insertOne($headers);
    }

    private function insertData(): void
    {
        $data = $this->submissions()->map(function ($submission) {
            $submission = $submission->toArray();
            $submission['date'] = (string) $submission['date'];
            unset($submission['id']);

            return collect($submission)
                ->map(fn ($value) => $this->stringify($value))
                ->all();
        })->all();

        $this->writer->insertAll($data);
    }

    private function stringify(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            return (string) $value;
        }

        if (is_array($value)) {
            return collect($value)
                ->map(fn ($item) => $this->stringify($item))
                ->filter(fn ($item) => $item !== '')
                ->implode(', ');
        }

        return '';
    }
}
