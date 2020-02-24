import React from 'react';
import ContentHeader from '../../content-header';
import LoadingContent from '../../loading-content';
import Breadcrumbs from '../../common/Breadcrumbs';
import $ from 'jquery';
import MultipleChart, { 
    TimeIntervalValues, MultipleChartProps
} from '../../charts/MultipleChart';
import SingleChart, { SingleChartProps, TimeIntervalValue } from '../../charts/SingleChart';

interface RoutesChartsState {
    summary: SingleChartProps,
    counts: MultipleChartProps,
    durations: MultipleChartProps
}

interface RouteCountsAPIResultItem extends TimeIntervalValue {}

interface RoutesMultipleStatsAPIResult {
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
            summary: null,
            counts: null,
            durations: null
        }
    }

    public componentDidMount() {
        this.loadCountStatistics('/api/stats/counts/route')
            .then((result) => this.setState({ summary: result }));
        this.loadParamStatistics('/api/stats/routes/count')
            .then((result) => this.setState({ counts: result }));
        this.loadParamStatistics('/api/stats/routes/durations')
            .then((result) => this.setState({ durations: result }));
    }

    public render(): React.ReactNode {
        const basePaths = [
            { 'name': 'Мониторинг' },
            { 'name': 'Маршруты' },
            { 'name': 'Статистика' }
        ];
        return this.state.summary !== null 
            && this.state.counts !== null 
            && this.state.durations !== null
            ? <>
                <ContentHeader>
                    <Breadcrumbs items={[...basePaths, {
                        'name': 'Общее количество'
                    }]} />
                </ContentHeader>
                <SingleChart intervals={this.state.summary.intervals} />
                <ContentHeader>
                    <Breadcrumbs items={[...basePaths, {
                        'name': 'Макс. количество'
                    }]} />
                </ContentHeader>
                <MultipleChart intervals={this.state.counts.intervals} />
                <ContentHeader>
                    <Breadcrumbs items={[...basePaths, {
                        'name': 'Макс. время' 
                    }]} />
                </ContentHeader>
                <MultipleChart intervals={this.state.durations.intervals} />
            </>
            : <>
                <ContentHeader>
                    <Breadcrumbs items={basePaths} />
                </ContentHeader>
                <LoadingContent></LoadingContent>
            </>
    }

    private loadCountStatistics(apiUrl: string): Promise<SingleChartProps> {
        return new Promise<SingleChartProps>((resolve, reject) => {
            $.ajax({
                url: apiUrl,
                dataType: 'json',
                success: (result: RouteCountsAPIResultItem[]) => {
                    resolve({ intervals: result });
                },
                error: (jqXHR, textStatus, errorThrown) => {
                    reject(apiUrl + ' error:' + errorThrown);
                }
            })
        })
    }

    private loadParamStatistics(apiUrl: string): Promise<MultipleChartProps> {
        return new Promise<MultipleChartProps>((resolve, reject) => {
            $.ajax({
                url: apiUrl,
                dataType: 'json',
                success: (result: RoutesMultipleStatsAPIResult) => {
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