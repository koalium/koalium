export type ActionType = {
    type: string
    name: string
    plans: string[]
}

export type ActionTypes = {[key: string]: ActionType}

export type DisplayModes = {[key: string]: string}