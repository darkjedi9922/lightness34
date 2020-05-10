import React from 'react';
import {
    AreaChart, Area, CartesianGrid, XAxis, YAxis, Tooltip, ResponsiveContainer
} from 'recharts';
import Table from '../../table/table';
import { round, maxBy, meanBy, sumBy } from 'lodash';
import { ChartProps, SecondInterval } from '../_common';
import ContentHeader from '../../content-header';
import Breadcrumbs from '../../common/Breadcrumbs';
import $ from 'jquery';

interface TimeIntervalValue {
    time: string,
    value: number
}

interface SingleChartState {
    intervals: TimeIntervalValue[],
    secondInterval: SecondInterval
    intervalCount: number,
}

class SingleChart extends React.Component<ChartProps, SingleChartState> {
    private lineColor = '#3F51B5'; // blue
    
    public constructor(props: ChartProps) {
        super(props);
        this.state = {
            intervals: null,
            secondInterval: SecondInterval.DAY,
            intervalCount: 10
        }
    }

    public componentDidMount() {
        this.loadChartData(this.props.onInitLoad);
    }

    public render(): React.ReactNode {
        const props = this.props;
        const state = this.state;
        if (!props.isReady) return <></>;
        return <>
            <ContentHeader>
                <Breadcrumbs items={[...props.basePaths, { 'name': props.title }]} />
            </ContentHeader>
            <div className="box chart">
                <ResponsiveContainer height={200} width="99%">
                    <AreaChart
                        data={state.intervals}
                        margin={{top: 10, right: 30, left: -20, bottom: 10}}
                    >
                        <CartesianGrid strokeDasharray="3 3" />
                        <XAxis dataKey="time" />
                        <YAxis />
                        <Tooltip isAnimationActive={false} />
                        <Area
                            type="monotone"
                            dataKey="value"
                            fill={this.lineColor}
                            stroke={this.lineColor}
                            fillOpacity={0.05}
                            dot={{ r: 3 }}
                            activeDot={{ r: 4 }}
                        />
                    </AreaChart>
                </ResponsiveContainer>
                <Table 
                    className="chart-table"
                    headers={['Summary count', 'Max', 'Avg']}
                    items={[{
                        cells: [
                            sumBy(state.intervals, 'value'),
                            maxBy(state.intervals, 'value').value,
                            round(meanBy(state.intervals, 'value'), 3)
                        ]
                    }]}
                />
            </div>
        </>
    }

    private loadChartData(setFinished?: () => void) {
        this.loadSingleStats().then((result) => {
            this.setState({ intervals: result })
            setFinished && setFinished();
        });
    }

    private loadSingleStats(): Promise<TimeIntervalValue[]> {
        return new Promise<TimeIntervalValue[]>((resolve, reject) => {
            $.ajax({
                url: this.props.apiUrl,
                dataType: 'json',
                success: (result: TimeIntervalValue[]) => {
                    resolve(result);
                },
                error: (jqXHR, textStatus, errorThrown) => {
                    reject(this.props.apiUrl + ' error:' + errorThrown);
                }
            })
        })
    }
}

export default SingleChart;