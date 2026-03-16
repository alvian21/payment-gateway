<script setup lang="ts">
import type { HTMLAttributes } from "vue"
import { Menu } from "lucide-vue-next"
import { cn } from "@/lib/utils"
import { Button } from '@/components/ui/button'
import { useSidebar } from "./utils"

const props = defineProps<{
  class?: HTMLAttributes["class"]
}>()

const { isMobile, toggleSidebar } = useSidebar()
</script>

<template>
  <Button
    data-sidebar="trigger"
    data-slot="sidebar-trigger"
    variant="ghost"
    size="icon"
    :class="cn('h-9 w-9', props.class)"
    @click="toggleSidebar"
  >
    <Menu class="h-5 w-5" />
    <span class="sr-only">Toggle sidebar</span>
  </Button>
</template>
