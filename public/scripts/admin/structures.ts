export interface Subscriber {
    id: number,
    event: string,
    class: string
}

export interface Emit {
    id: number,
    event: string
}

export interface Handle {
    emitId: number,
    subscriberId: number
}