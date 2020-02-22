import React from 'react';
import ContentHeader from '../../content-header';
import LoadingContent from '../../loading-content';
import Breadcrumbs from '../../common/Breadcrumbs';
import $ from 'jquery';
import SummaryChart, { 
    TimeIntervalValues, SummaryData, SummaryChartProps
} from '../../charts/SummaryChart';

interface RoutesChartsState {
    counts: SummaryChartProps
}

interface RoutesCountAPIResult {
    [url: string]: {
        counts: [{
            count: number,
            time: string,
            timestamp: number
        }],
        max: number,
        avg: number
    }
}

class RoutesCharts extends React.Component<{}, RoutesChartsState> {
    public constructor(props) {
        super(props);
        this.state = {
            counts: {
                objects: {},
                values: []
            }
        }
    }

    public componentDidMount() {
        this.loadCounts();
    }

    public render(): React.ReactNode {
        const basePaths = [
            { 'name': 'Мониторинг' },
            { 'name': 'Маршруты' },
            { 'name': 'Статистика' }
        ];
        return this.state.counts.values.length
            ? <>
                <ContentHeader>
                    <Breadcrumbs items={[...basePaths, { 'name': 'Количество' }]} />
                </ContentHeader>
                <SummaryChart
                    objects={this.state.counts.objects}
                    values={this.state.counts.values}
                />
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
            success: (result: RoutesCountAPIResult) => {
                this.setState({
                    counts: (() => {
                        const resultCounts: TimeIntervalValues[] = [];
                        for (const url in result) {
                            if (result.hasOwnProperty(url)) {
                                const urlData = result[url];
                                // Все url в ответе имеют одинаковое количество
                                // временных интервалов, а последние одинаковые.
                                for (let i = 0; i < urlData.counts.length; i++) {
                                    const countData = urlData.counts[i];
                                    // Поэтому будем обновлять те же интервалы,
                                    // добавляя в них каждый новый url.
                                    if (!resultCounts[i]) resultCounts[i] = {
                                        time: countData.time,
                                        values: {}
                                    }
                                    resultCounts[i].values[url] = countData.count;
                                }
                            }
                        }

                        const resultUrls: {[url: string]: SummaryData} = {};
                        for (const url in result) {
                            if (result.hasOwnProperty(url)) {
                                const urlData = result[url];
                                resultUrls[url] = {
                                    max: urlData.max,
                                    avg: urlData.avg
                                }
                            }
                        }

                        return {
                            objects: resultUrls,
                            values: resultCounts
                        };
                    })()
                })
            }
        })
    }
}

export default RoutesCharts;