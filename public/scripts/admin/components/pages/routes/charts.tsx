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
                times: []
            },
            durations: {
                times: []
            }
        }
    }

    public componentDidMount() {
        this.loadCounts();
        this.loadDurations();
    }

    public render(): React.ReactNode {
        const basePaths = [
            { 'name': 'Мониторинг' },
            { 'name': 'Маршруты' },
            { 'name': 'Статистика' }
        ];
        return this.state.counts.times.length && this.state.durations.times.length
            ? <>
                <ContentHeader>
                    <Breadcrumbs items={[...basePaths, { 'name': 'Количество' }]} />
                </ContentHeader>
                <SummaryChart times={this.state.counts.times} />
                <ContentHeader>
                    <Breadcrumbs items={[...basePaths, { 'name': 'Макс. время' }]} />
                </ContentHeader>
                <SummaryChart times={this.state.durations.times} />
            </>
            : <>
                <ContentHeader>
                    <Breadcrumbs items={basePaths} />
                </ContentHeader>
                <LoadingContent></LoadingContent>
            </>
    }

    private loadCounts(): void {
        $.ajax({
            url: '/api/stats/routes/count',
            dataType: 'json',
            success: (result: RoutesSummaryAPIResult) => {
                this.setState({
                    counts: (() => {
                        const resultCounts: TimeIntervalValues[] = [];
                        for (const url in result) {
                            if (result.hasOwnProperty(url)) {
                                const urlData = result[url];
                                // Все url в ответе имеют одинаковое количество
                                // одинаковых временных интервалов.
                                for (let i = 0; i < urlData.length; i++) {
                                    const countData = urlData[i];
                                    // Поэтому будем обновлять те же интервалы,
                                    // добавляя в них каждый новый url.
                                    if (!resultCounts[i]) resultCounts[i] = {
                                        time: countData.time,
                                        values: {}
                                    }
                                    resultCounts[i].values[url] = countData.value;
                                }
                            }
                        }

                        return {
                            times: resultCounts
                        };
                    })()
                })
            }
        })
    }

    private loadDurations(): void {
        $.ajax({
            url: '/api/stats/routes/durations',
            dataType: 'json',
            success: (result: RoutesSummaryAPIResult) => {
                this.setState({
                    durations: (() => {
                        const resultCounts: TimeIntervalValues[] = [];
                        for (const url in result) {
                            if (result.hasOwnProperty(url)) {
                                const urlData = result[url];
                                // Все url в ответе имеют одинаковое количество
                                // одинаковых временных интервалов.
                                for (let i = 0; i < urlData.length; i++) {
                                    const countData = urlData[i];
                                    // Поэтому будем обновлять те же интервалы,
                                    // добавляя в них каждый новый url.
                                    if (!resultCounts[i]) resultCounts[i] = {
                                        time: countData.time,
                                        values: {}
                                    }
                                    resultCounts[i].values[url] = countData.value;
                                }
                            }
                        }

                        return {
                            times: resultCounts
                        };
                    })()
                })
            }
        })
    }
}

export default RoutesCharts;