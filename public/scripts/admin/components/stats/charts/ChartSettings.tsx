import React from 'react';
import Form, { RadioField } from '../../form/Form';
import { SortOrder } from '../../table/Table';
import classNames from 'classnames';
import { SortColumn } from '../_common';

export interface ChartSettingsData {
    sortField: SortColumn,
    sortOrder: SortOrder,
    secondInterval: number
}

interface SettingsProps {
    onUpdate: (newData: ChartSettingsData, setFinished: () => void) => void,
    multipleSettings: boolean
}

interface SettingsState {
    sortField: SortColumn,
    sortOrder: SortOrder,
    intervalUnit: string,
    secondInterval: number,
    isUpdating: boolean
}

class ChartSettings extends React.Component<SettingsProps, SettingsState> {
    private secondIntervals = {
        'Минуты': {
            '5 минут': 60 * 5,
            '10 минут': 60 * 10,
            '15 минут': 60 * 15,
            '30 минут': 60 * 30,
            '45 минут': 60 * 45
        },
        'Часы': {
            '1 час': 60 * 60,
            '2 часа': 60 * 60 * 2,
            '6 часов': 60 * 60 * 6,
            '12 часов': 60 * 60 * 12,
        },
        'Дни': {
            '1 день': 60 * 60 * 24,
            '7 дней': 60 * 60 * 24 * 7,
            '14 дней': 60 * 60 * 24 * 14,
            '28 дней': 60 * 60 * 24 * 28,
        },
        'Месяцы': {
            '1 месяц': 60 * 60 * 24 * 30,
            '3 месяца': 60 * 60 * 24 * 30 * 3,
            '6 месяцев': 60 * 60 * 24 * 30 * 6,
            '9 месяцев': 60 * 60 * 24 * 30 * 9,
        },
        'Годы': {
            '1 год': 60 * 60 * 24 * 30 * 12,
            '2 года': 60 * 60 * 24 * 30 * 12 * 2,
            '3 года': 60 * 60 * 24 * 30 * 12 * 3,
            '5 лет': 60 * 60 * 24 * 30 * 12 * 5,
        }
    };

    private lastIntervals: { [interval: string]: number };

    public constructor(props: SettingsProps) {
        super(props);
        this.state = {
            sortField: SortColumn.AVG,
            sortOrder: SortOrder.DESC,
            intervalUnit: 'Дни',
            secondInterval: this.secondIntervals['Дни']['1 день'],
            isUpdating: false
        }

        this.lastIntervals = {};
        for (const interval in this.secondIntervals) {
            if (this.secondIntervals.hasOwnProperty(interval)) {
                const secondIntervals = this.secondIntervals[interval];
                this.lastIntervals[interval] = Object.values(secondIntervals)[0] as number;
            }
        }

        this.onUpdateClick = this.onUpdateClick.bind(this);

    }

    public render(): React.ReactElement {
        const props = this.props;
        return <div className="box-settings">
            {props.multipleSettings &&
                <div className="box-settings__column">
                    <Form
                        className="chart-form"
                        method="get"
                        fields={[{
                            type: 'radio',
                            title: 'Значение',
                            name: 'field',
                            values: [
                                { label: 'Среднее', value: SortColumn.AVG },
                                { label: 'Максимальное', value: SortColumn.MAX }
                            ],
                            currentValue: this.state.sortField,
                            onChange: (event) => this.setState({ sortField: event.target.value as SortColumn })
                        } as RadioField, {
                            type: 'radio',
                            title: 'Сортировка',
                            name: 'sort',
                            values: [
                                { label: 'По убыванию', value: SortOrder.DESC },
                                { label: 'По возврастанию', value: SortOrder.ASC }
                            ],
                            currentValue: this.state.sortOrder,
                            onChange: (event) => this.setState({ sortOrder: event.target.value as SortOrder })
                        } as RadioField]}
                    />
                </div>
            }
            <div className="box-settings__column">
                <Form
                    className="chart-form"
                    method="get"
                    fields={[{
                        type: 'radio',
                        title: 'Единицы интервалов',
                        name: 'interval-name',
                        values: Object.keys(this.secondIntervals).map((name) => ({
                            label: name, value: name
                        })),
                        currentValue: this.state.intervalUnit,
                        onChange: (event) => this.setState({ 
                            intervalUnit: event.target.value,
                            secondInterval: this.lastIntervals[event.target.value]
                        })
                    } as RadioField]}
                />
            </div>
            <div className="box-settings__column">
                <Form
                    className="chart-form"
                    method="get"
                    fields={[{
                        type: 'radio',
                        title: 'Значения интервалов',
                        name: 'interval-sec',
                        values: Object.keys(this.secondIntervals[this.state.intervalUnit]).map((key) => ({
                            label: key, value: this.secondIntervals[this.state.intervalUnit][key].toString()
                        })),
                        currentValue: this.state.secondInterval.toString(),
                        onChange: (event) => {
                            let seconds = Number.parseInt(event.target.value);
                            this.lastIntervals[this.state.intervalUnit] = seconds;
                            this.setState({ secondInterval: seconds })
                        }
                    } as RadioField]}
                />
            </div>
            <button className="box-settings__button" onClick={this.onUpdateClick}>
                <i className={classNames([
                    'icon-spin3',
                    {'animate-spin': this.state.isUpdating}
                ])}></i>
            </button>
        </div>
    }

    private onUpdateClick() {
        this.setState({ isUpdating: true }, () => {
            this.props.onUpdate({
                sortField: this.state.sortField,
                sortOrder: this.state.sortOrder,
                secondInterval: this.state.secondInterval
            }, () => {
                this.setState({ isUpdating: false })
            })
        })
    }
}

export default ChartSettings;