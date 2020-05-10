import { BreadcrumbsItem } from "../common/Breadcrumbs";

export enum SecondInterval {
    HOUR = 60 * 60,
    DAY = HOUR * 24,
    WEEK = DAY * 7,
    MONTH = DAY * 30
}

export interface ChartProps {
    title: string,
    apiUrl: string,
    basePaths: BreadcrumbsItem[],
    isReady: boolean,
    onInitLoad: () => void
}