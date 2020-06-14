import { BreadcrumbsItem } from "../common/Breadcrumbs";

export enum SecondInterval {
    HOUR = 60 * 60,
    DAY = HOUR * 24,
    WEEK = DAY * 7,
    MONTH = DAY * 30
}

export enum SortColumn {
    MAX = 'max',
    AVG = 'avg'
}

export interface ChartProps {
    title: string,
    apiUrl: string,
    basePaths: BreadcrumbsItem[],
    isReady: boolean,
    objectName: string,
    valueUnit?: string,
    onInitLoad: () => void
}