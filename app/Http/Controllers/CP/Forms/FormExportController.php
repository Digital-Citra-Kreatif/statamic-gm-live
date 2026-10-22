<?php

namespace App\Http\Controllers\CP\Forms;

use Statamic\Exceptions\NotFoundHttpException;
use Statamic\Http\Controllers\CP\Forms\FormExportController as StatamicFormExportController;
use Statamic\Http\Requests\FilteredRequest;

class FormExportController extends StatamicFormExportController
{
    public function export(FilteredRequest $request, $form, $type)
    {
        $this->authorize('view', $form);

        if (! $exporter = $form->exporter($type)) {
            throw new NotFoundHttpException;
        }

        if ($this->shouldApplyFilteredScope($request)) {
            $exporter->setSubmissions($this->getScopedSubmissions($request, $form));
        }

        if (! $request->has('download')) {
            return $exporter->response();
        }

        // Stream unduhan
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $filename = $form->handle().'-'.now()->format('Y-m-d-His').'.'.$exporter->extension();

        return response()->streamDownload(function () use ($exporter) {
            echo $exporter->export();
        }, $filename, [
            'Content-Type' => method_exists($exporter, 'contentType')
                ? $exporter->contentType()
                : 'text/csv; charset=UTF-8',
        ]);
    }
}
