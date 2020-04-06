<?php namespace engine\statistics\macros\actions;

use frame\route\Request;
use frame\actions\Action;
use frame\actions\ActionBody;
use frame\actions\fields\UploadedFile;
use engine\statistics\stats\ActionStat;
use engine\statistics\stats\TimeStat;
use engine\statistics\macros\BaseStatCollector;
use frame\actions\fields\PasswordField;

use function lightlib\shorten;
use function lightlib\decode_specials;
use function lightlib\encode_specials;

class StartCollectActionStat extends BaseStatCollector
{
    private $stat;
    private $timer;

    public function __construct(ActionStat $stat, TimeStat $timer)
    {
        $this->stat = $stat;
        $this->timer = $timer;
    }

    protected function collect(...$args)
    {
        /** @var Action $action */
        $action = $args[0];
        
        $class = get_class($action->getBody());
        $this->stat->class = str_replace('\\', '\\\\', $class);
        $this->stat->ajax = Request::getDriver()->isAjax();
        $this->stat->data_json = str_replace('\\', '\\\\', $this->jsonify($action));
        $this->stat->time = time();

        $this->timer->start();
    }

    protected function jsonify(Action $action): string
    {
        $data = $action->getDataArray(true);
        $get = $data[Action::ARGS];
        $post = $this->shortenAndFilterPostData($action);
        $files = $this->toArrayFiles($data[Action::FILES]);

        return json_encode([
            'errors' => $action->getErrors(),
            'data' => [
                'get' => $get,
                'post' => $post,
                'files' => $files
            ],
            'result' => $action->getResult()
        ], JSON_HEX_AMP);
    }

    private function shortenAndFilterPostData(Action $action): array
    {
        $result = [];
        $desc = $action->getBody()->listPost();
        $data = $action->getDataArray(true)['post'];
        foreach ($data as $field => $value) {
            $newValue = $value;
            $type = ($desc[$field] ?? null);
            if (   $type === PasswordField::class
                || is_subclass_of($type, PasswordField::class)
            ) {
                $newValue = ($newValue !== '' ? 'secret' : '');
            } else if (is_string($value)) {
                $newValue = shorten(decode_specials($value), 50, '...');
                $newValue = encode_specials($newValue);
            }
            $result[$field] = $newValue;
        }
        return $result;
    }

    private function toArrayFiles(array $files): array
    {
        $result = [];
        foreach ($files as $field => $file) {
            /** @var UploadedFile $file */
            $result[$field] = $file->toArray();
            $result[$field]['mime'] = $file->isLoaded() ? $file->getMime() : '';
        }
        return $result;
    }
}