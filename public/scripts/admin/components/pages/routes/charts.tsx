import React from 'react';
import ContentHeader from '../../content-header';
import LoadingContent from '../../loading-content';
import Breadcrumbs from '../../common/Breadcrumbs';
import $ from 'jquery';
import SummaryChart, { 
    TimeIntervalValues, SummaryChartProps
} from '../../charts/SummaryChart';

interface RoutesChartsState {
    counts: SummaryChartProps,
    durations: SummaryChartProps
}

interface RoutesSummaryAPIResult {
    [url: string]: [{
        value?: number,
        time: string,
        timestamp: number
    }]
}

class RoutesCharts extends React.Component<{}, RoutesChartsState> {
    public constructor(props) {
        super(props);
        this.state = {
            counts: {
                intervals: []
            },
            durations: {
                intervals: []
            }
        }
    }

    public componentDidMount() {
        this.loadStatistics('/api/stats/routes/count')
            .then((result) => this.setState({ counts: result }));
        this.loadStatistics('/api/stats/routes/durations')
            .then((result) => this.setState({ durations: result }));
    }

    public render(): React.ReactNode {
        const basePaths = [
            { 'name': 'Мониторинг' },
            { 'name': 'Маршруты' },
            { 'name': 'Статистика' }
        ];
        return this.state.counts.intervals.length 
            && this.state.durations.intervals.length
            ? <>
                <ContentHeader>
                    <Breadcrumbs items={[...basePaths, { 'name': 'Количество' }]} />
                </ContentHeader>
                <SummaryChart intervals={this.state.counts.intervals} />
                <ContentHeader>
                    <Breadcrumbs items={[...basePaths, { 'name': 'Макс. время' }]} />
                </ContentHeader>
                <SummaryChart intervals={this.state.durations.intervals} />
            </>
            : <>
                <ContentHeader>
                    <Breadcrumbs items={basePaths} />
                </ContentHeader>
                <LoadingContent></LoadingContent>
            </>
    }

    private loadStatistics(apiUrl: string): Promise<SummaryChartProps> {
        return new Promise<SummaryChartProps>((resolve, reject) => {
            $.ajax({
                url: apiUrl,
                dataType: 'json',
                success: (result: RoutesSummaryAPIResult) => {
                    const intervals: TimeIntervalValues[] = [];
                    for (const objectName in result) {
                        if (result.hasOwnProperty(objectName)) {
                            const data = result[objectName];
                            // Все objectName в ответе имеют одинаковое количество
                            // одинаковых временных интервалов.
                            for (let i = 0; i < data.length; i++) {
                                const valueData = data[i];
                                // Поэтому будем обновлять те же интервалы,
                                // добавляя в них каждый новый objectName.
                                if (!intervals[i]) intervals[i] = {
                                    time: valueData.time,
                                    values: {}
                                }
                                intervals[i].values[objectName] = valueData.value;
                            }
                        }
                    }
                    resolve({ intervals: intervals });
                },
                error: (jqXHR, textStatus, errorThrown) => {
                    reject(apiUrl + ' error:' + errorThrown);
                }
            })
        })
    }
}

export default RoutesCharts;