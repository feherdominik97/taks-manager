export const useTask = () => {
    const getDefaultTask = () => {
        return {
            id: 0,
            duration: 0,
            is_done: false,
            assignees: [],
            name: "Feladat",
            priority: "normál",
            scheduled_day: new Date(),
            created_at: "",
            updated_at: "",
        }
    }

    const formatDate = (date: Date): string => {
        const year = date.getFullYear()
        const month = (date.getMonth() + 1).toString().padStart(2, '0')
        const day = date.getDate().toString().padStart(2, '0')

        return `${year}-${month}-${day}`
    }

    return { getDefaultTask, formatDate }
}