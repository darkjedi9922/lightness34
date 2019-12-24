<?php namespace engine\statistics\macros\actions;

use frame\actions\Action;
use frame\actions\ActionBody;
use engine\statistics\stats\ActionStat;
use engine\statistics\stats\TimeStat;
use engine\statistics\macros\BaseStatCollector;

use function lightlib\shorten;
use function lightlib\decode_specials;
use function lightlib\encode_specials;

class EndCollectActionStat extends BaseStatCollector
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

        $this->stat->duration_sec = $this->timer->resultInSeconds();
        $this->stat->data_json = str_replace('\\', '\\\\', $this->jsonify($action));
    }

    protected function jsonify(Action $action): string
    {
        $data = $action->getDataArray();
        $get = $data[Action::ARGS];
        $post = $this->shortenAndFilterPostData($action);
        $files = $this->stat->getHandledFiles();

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
        $data = $action->getDataArray()['post'];
        foreach ($data as $field => $value) {
            $newValue = $value;
            if (($desc[$field][0] ?? null) === ActionBody::POST_PASSWORD) 
                $newValue = ($newValue !== '' ? 'secret' : '');
            else if (is_string($value)) {
                $newValue = shorten(decode_specials($value), 50, '...');
                $newValue = encode_specials($newValue);
            }
            $result[$field] = $newValue;
        }
        return $result;
    }
}