import React from 'react';
import Breadcrumbs from '../../common/Breadcrumbs';
import $ from 'jquery';
import { isNil } from 'lodash';
import LoadingContent from '../../loading-content';
import SingleChart, { TimeIntervalValue } from '../../stats/charts/SingleChart';

interface APIQueriesResultItem extends TimeIntervalValue {}

interface QueriesPageState {
    intervals: TimeIntervalValue[]
}

class QueriesPage extends React.Component<{}, QueriesPageState> {
    public constructor(props) {
        super(props);
        this.state = { intervals: null }
    }

    public componentDidMount(): void {
        $.ajax({
            url: '/api/stats/queries/summary',
            dataType: 'json',
            success: (result: APIQueriesResultItem[]) => {
                this.setState({
                    intervals: result
                });
            }
        })
    }

    public render(): React.ReactNode {
        return <>
            <div className="content__header">
                <div className="breadcrumbs-wrapper">
                    <Breadcrumbs items={[
                        { name: 'Мониторинг' },
                        { name: 'База данных' },
                        { name: 'Запросы' }
                    ]} />
                </div>
            </div>
            <LoadingContent>
                {!isNil(this.state.intervals) &&
                    <SingleChart intervals={this.state.intervals} />
                }
            </LoadingContent>
        </>;
    }
}

export default QueriesPage;