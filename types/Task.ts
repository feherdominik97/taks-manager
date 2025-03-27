export interface Task {
    id: number
    duration: number
    is_done: boolean
    assignees: string[]
    name: string
    priority: "alacsony" | "normál" | "magas"
    scheduled_day: string
    created_at: string
    updated_at: string
}