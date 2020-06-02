import { MarkColor } from "../common/_common";

export interface LogLevel {
    name: string,
    color: MarkColor,
    icon: string
}

export interface LogLevelSetting extends LogLevel {
    checked: boolean
}