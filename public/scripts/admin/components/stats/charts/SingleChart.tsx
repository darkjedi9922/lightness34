import React from 'react';
import {
    AreaChart, Area, CartesianGrid, XAxis, YAxis, Tooltip, ResponsiveContainer
} from 'recharts';
import Table, { SortOrder } from '../../table/Table';
import { round, maxBy, meanBy, sumBy } from 'lodash';
import { ChartProps, SecondInterval, SortColumn } from '../_common';
import ContentHeader from '../../content/ContentHeader';
import Breadcrumbs from '../../common/Breadcrumbs';
import $ from 'jquery';
import ChartSettings, { ChartSettingsData } from './ChartSettings';

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
        this.onChartUpdate = this.onChartUpdate.bind(this);
    }

    public componentDidMount() {
        this.loadChartData({
            sortField: SortColumn.AVG, // will not be used
            sortOrder: SortOrder.DESC, // will not be used
            secondInterval: this.state.secondInterval
        }, this.props.onInitLoad);
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
                        margin={{top: 10, right: 30, left: -10, bottom: 10}}
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
                <div className="box__details">
                    <span className="box__header">Настройки</span>
                    <ChartSettings
                        onUpdate={this.onChartUpdate}
                        multipleSettings={false}
                    />
                </div>
            </div>
        </>
    }

    private loadChartData(settings: ChartSettingsData, setFinished?: () => void) {
        this.loadSingleStats(settings).then((result) => {
            this.setState({
                intervals: result,
                secondInterval: settings.secondInterval
            })
            setFinished && setFinished();
        });
    }

    private loadSingleStats(settings: ChartSettingsData): Promise<TimeIntervalValue[]> {
        return new Promise<TimeIntervalValue[]>((resolve, reject) => {
            $.ajax({
                url: this.props.apiUrl,
                data: {
                    sec_interval: settings.secondInterval
                },
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

    private onChartUpdate(newSettings: ChartSettingsData, setFinished: () => void) {
        this.loadChartData({
            sortField: SortColumn.AVG, // will not be used
            sortOrder: SortOrder.DESC, // will not be used
            secondInterval: newSettings.secondInterval
        }, setFinished)
    }
}

export default SingleChart;