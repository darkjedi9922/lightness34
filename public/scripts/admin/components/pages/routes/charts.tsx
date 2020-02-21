import React from 'react';
import ContentHeader from '../../content-header';
import LoadingContent from '../../loading-content';
import Breadcrumbs from '../../common/Breadcrumbs';
import {
    AreaChart, Area, CartesianGrid, XAxis, YAxis, Tooltip, ResponsiveContainer
} from 'recharts';
import $ from 'jquery';
import Table from '../../table/table';

interface TimeIntervalCounts {
    time: string,
    counts: {[url: string]: any}
}

interface UrlSummaryCount {
    max: number,
    avg: number
}

interface RoutesChartsState {
    urls: {[url: string]: UrlSummaryCount}
    counts: TimeIntervalCounts[]
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
    private lineColors = [
        '#3F51B5', // blue
        '#4CAF50', // green
        '#F44336', // red
        '#b3ab00', // yellow
        '#9c27b0' // purple
    ];

    public constructor(props) {
        super(props);
        this.state = {
            urls: {},
            counts: []
        }
    }

    public componentDidMount() {
        this.loadCounts();
    }

    public render(): React.ReactNode {
        return <>
            <ContentHeader>
                <Breadcrumbs items={[
                    { 'name': 'Мониторинг' },
                    { 'name': 'Маршруты' },
                    { 'name': 'Статистика' },
                    { 'name': 'Количество' }
                ]} />
            </ContentHeader>
            <LoadingContent>
                {this.state.counts.length &&
                    <div className="box chart">
                        <ResponsiveContainer height={250} width="99%">
                            <AreaChart
                                data={this.state.counts}
                                margin={{
                                    top: 10, right: 30, left: -20, bottom: 10,
                                }}
                            >
                                <CartesianGrid strokeDasharray="3 3" />
                                <XAxis dataKey="time" />
                                <YAxis />
                                <Tooltip isAnimationActive={false} />
                                {Object.keys(this.state.urls)
                                    .map((url: string, index: number) => (
                                        <Area
                                            key={index}
                                            type="monotone"
                                            name={url}
                                            dataKey={`counts[${url}]`}
                                            fill={this.getColorByNumber(index)}
                                            stroke={this.getColorByNumber(index)}
                                            fillOpacity={0.05}
                                            dot={false}
                                            activeDot={{ r: 4, className: 'chart__dot' }}
                                        />
                                ))}
                            </AreaChart>
                        </ResponsiveContainer>
                        <Table 
                            className="chart-table"
                            headers={['Route', 'Max', 'Avg']}
                            items={Object.keys(this.state.urls)
                                .map((url: string, index: number) => {
                                    return {
                                        cells: [
                                            <>
                                                <i className="
                                                    icon-bookmark 
                                                    chart-table__color-icon
                                                " style={{
                                                    color: this.getColorByNumber(index)
                                                }}></i>
                                                {url}
                                            </>,
                                            this.state.urls[url].max,
                                            this.state.urls[url].avg
                                        ]
                                    }
                                })
                            }
                        />
                    </div>
                }
            </LoadingContent>
        </>;
    }

    private loadCounts(): void {
        $.ajax({
            url: '/api/stats/routes/count',
            dataType: 'json',
            success: (result: RoutesCountAPIResult) => {
                this.setState({
                    urls: (() => {
                        const resultUrls: {[url: string]: UrlSummaryCount} = {};
                        for (const url in result) {
                            if (result.hasOwnProperty(url)) {
                                const urlData = result[url];
                                resultUrls[url] = {
                                    max: urlData.max,
                                    avg: urlData.avg
                                }
                            }
                        }
                        return resultUrls;
                    })(),
                    counts: (() => {
                        const resultCounts: TimeIntervalCounts[] = [];
                        console.dir(result);
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
                                        counts: {}
                                    }
                                    resultCounts[i].counts[url] = countData.count;
                                }
                            }
                        }
                        return resultCounts;
                    })()
                })
            }
        })
    }

    private getColorByNumber(number: number): string {
        return this.lineColors[number % this.lineColors.length];
    }
}

export default RoutesCharts;