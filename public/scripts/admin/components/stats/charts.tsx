import React from 'react';
import ContentHeader  from '../content-header';
import LoadingContent from '../loading-content';
import Breadcrumbs from '../common/Breadcrumbs';
import MultipleChart from './charts/MultipleChart';
import SingleChart from './charts/SingleChart';

interface ChartsProps {
    name: string,
    stat: string
}

interface ChartsState {
    loadedChartsCount: number
}

class StatCharts extends React.Component<ChartsProps, ChartsState> {
    public constructor(props: ChartsProps) {
        super(props);
        this.state = { loadedChartsCount: 0 };
        this.onChartInitLoad = this.onChartInitLoad.bind(this);
    }

    public render(): React.ReactNode {
        const basePaths = [
            { 'name': 'Мониторинг' },
            { 'name': this.props.name },
            { 'name': 'Статистика' }
        ];
        const isAllLoaded = this.state.loadedChartsCount == 3;
        return <>
            <SingleChart
                title='Общее количество'
                apiUrl={`/api/stats/${this.props.stat}/summary`}
                basePaths={basePaths}
                isReady={isAllLoaded}
                onInitLoad={this.onChartInitLoad}
            />
            <MultipleChart
                title='По количеству'
                apiUrl={`/api/stats/${this.props.stat}/count`}
                basePaths={basePaths}
                isReady={isAllLoaded}
                onInitLoad={this.onChartInitLoad}
            />
            <MultipleChart
                title='По времени'
                apiUrl={`/api/stats/${this.props.stat}/durations`}
                basePaths={basePaths}
                isReady={isAllLoaded}
                onInitLoad={this.onChartInitLoad}
            />
            {!isAllLoaded && <>
                <ContentHeader>
                    <Breadcrumbs items={basePaths} />
                </ContentHeader>
                <LoadingContent></LoadingContent>
            </>}
        </>
    }

    private onChartInitLoad(): void {
        this.setState((state) => ({
            loadedChartsCount: state.loadedChartsCount + 1
        }));
    }
}

export default StatCharts;