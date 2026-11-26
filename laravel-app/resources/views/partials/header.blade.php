<?php $fullName=session("full_name", "Campus student"); $role=session("role", "Student"); $words=preg_split("/\s+/", trim($fullName)); $initials=strtoupper(substr($words[0],0,1).(isset($words[1]) ? substr($words[1],0,1) : "")); ?><!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle ?? 'UniFood | Campus food, made easy') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --forest: #1e3d29;
            --forest-hover: #152d1e;
            --accent-green: #3b6b47;
            --lime: #cbe896;
            --bg-page: #fbfbf8;
            --bg-sidebar: #fbfbf8;
            --card-light-green: #e9f0df;
            --text-dark: #1b261e;
            --text-muted: #6b776d;
            --border-color: #e5e9e2;
            --pill-active-bg: #1e3d29;
            --pill-inactive-bg: #ffffff;
            --pill-inactive-border: #e3e8e0;
            --sunshine: #f9d71c;
            --coral: #ff6f61;
            --sky-blue: #61a0f0;
            --lavender: #c79fe8;
            --cream: #fff9e6;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: "Plus Jakarta Sans", Inter, -apple-system, sans-serif;
            background-color: var(--bg-page);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            -webkit-font-smoothing: antialiased;
            animation: pageFadeIn 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes pageFadeIn {
            0% {
                opacity: 0;
                transform: scale(0.99);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        button,
        input,
        select,
        textarea {
            font: inherit;
        }

        /* Colourful accents */
        .drawer-mode-tab:hover {
            background-color: var(--sunshine);
            color: var(--forest);
        }

        .drawer-mode-tab.active {
            background-color: var(--coral);
            color: #fff;
        }

        .cat-pill:hover,
        .banner-cta:hover,
        .header-tag:hover,
        .fresh-picks:hover {
            background-color: var(--sky-blue);
            color: var(--forest);
        }

        .banner-dot.active {
            background-color: var(--coral);
        }

        .bag-drawer {
            background: linear-gradient(135deg, var(--cream) 0%, var(--card-light-green) 100%);
            border-left: 4px solid var(--sky-blue);
        }

        #summary-fee-value {
            color: var(--lavender);
            font-weight: 700;
        }

        /* SIDEBAR */
        aside.sidebar {
            width: 250px;
            background-color: var(--bg-sidebar);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            padding: 24px 18px 20px 20px;
            flex-shrink: 0;
            height: 100vh;
            position: sticky;
            top: 0;
            overflow-y: auto;
            z-index: 20;
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: inherit;
            cursor: pointer;
        }

        .logo-badge {
            width: 36px;
            height: 36px;
            background-color: var(--forest);
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--lime);
            transition: transform 0.2s;
        }

        .logo-area:hover .logo-badge {
            transform: rotate(-8deg) scale(1.04);
        }

        .logo-text {
            display: flex;
            flex-direction: column;
        }

        .logo-title {
            font-size: 21px;
            font-weight: 800;
            letter-spacing: -0.6px;
            color: var(--forest);
            display: flex;
            align-items: center;
            gap: 2px;
        }

        .logo-title span.dot {
            color: #9dc259;
            font-size: 13px;
        }

        .logo-tagline {
            font-size: 8.5px;
            letter-spacing: 1.4px;
            font-weight: 800;
            color: #a4b0a2;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .nav-links {
            margin-top: 32px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .nav-item {
            width: 100%;
            border: none;
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 14px;
            border-radius: 12px;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.15s ease;
            text-align: left;
        }

        .nav-item-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-item:hover {
            color: var(--text-dark);
            background-color: #f1f4ed;
        }

        .nav-item.active {
            background-color: #eaf1e3;
            color: var(--forest);
            font-weight: 700;
        }

        .nav-item.active svg {
            color: var(--forest);
        }

        .nav-badge {
            font-size: 11px;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 999px;
            background: #e1ead8;
            color: var(--forest);
            display: none;
        }

        .nav-badge.visible {
            display: inline-block;
        }

        /* Sidebar Eco Banner */
        .sidebar-eco {
            margin-top: auto;
            background: #eef2e7;
            border: 1.5px solid transparent;
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 16px;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }

        .sidebar-eco:hover {
            background: #e6ede0;
            transform: translateY(-1px);
        }

        .sidebar-eco.active {
            border-color: var(--forest);
            background: #e3eed8;
        }

        .eco-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .sidebar-eco .eco-icon {
            font-size: 17px;
            display: inline-block;
        }

        .eco-status-pill {
            font-size: 9.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            padding: 3px 7px;
            border-radius: 999px;
            background: rgba(30, 61, 41, 0.1);
            color: var(--forest);
        }

        .sidebar-eco.active .eco-status-pill {
            background: var(--forest);
            color: #ffffff;
        }

        .sidebar-eco h4 {
            font-size: 13px;
            font-weight: 800;
            color: #172b1d;
            line-height: 1.35;
            margin-bottom: 6px;
            letter-spacing: -0.2px;
        }

        .sidebar-eco p {
            font-size: 11.5px;
            color: #5d6e60;
            line-height: 1.48;
        }

        .sidebar-eco .eco-highlight {
            font-size: 13px;
            font-weight: 800;
            color: #162419;
            margin-top: 8px;
            display: block;
            letter-spacing: -0.3px;
        }

        /* Sidebar User Profile */
        .sidebar-user {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 14px;
            border-top: 1px solid var(--border-color);
            flex-shrink: 0;
        }

        .user-left {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: inherit;
            cursor: pointer;
            padding: 4px;
            margin: -4px;
            border-radius: 10px;
            transition: background 0.15s;
        }

        .user-left:hover {
            background: #f1f4ed;
        }

        .user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background-color: #e5ece0;
            color: #3b503e;
            font-size: 11.5px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-info h5 {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--text-dark);
        }

        .user-info p {
            font-size: 10.5px;
            color: #929d93;
        }

        .logout-btn {
            background: none;
            border: none;
            color: #9aa59b;
            cursor: pointer;
            padding: 6px;
            display: flex;
            align-items: center;
            border-radius: 8px;
            transition: all 0.15s;
        }

        .logout-btn:hover {
            color: #b93829;
            background: #faeaea;
        }

        /* MAIN CONTENT */
        main.main-content {
            flex: 1;
            padding: 24px 44px 60px;
            max-width: 1260px;
            min-width: 0;
        }

        /* Top Bar */
        .top-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
            position: relative;
        }

        .campus-selector-wrap {
            position: relative;
        }

        .campus-selector {
            font-size: 12.5px;
            color: var(--text-muted);
            display: flex;
            flex-direction: column;
            gap: 3px;
            background: transparent;
            border: none;
            text-align: left;
            cursor: pointer;
            padding: 6px 10px;
            margin: -6px -10px;
            border-radius: 12px;
            transition: background 0.15s;
        }

        .campus-selector:hover {
            background: #f2f5ee;
        }

        .campus-dropdown {
            font-weight: 800;
            color: var(--text-dark);
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .sub-location {
            color: #5b695d;
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .sub-location span.sparkle {
            color: #6b9a3d;
            font-size: 11px;
        }

        .campus-menu {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            width: 270px;
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            box-shadow: 0 16px 36px rgba(24, 39, 28, 0.12);
            padding: 8px;
            display: none;
            z-index: 50;
        }

        .campus-menu.open {
            display: block;
        }

        .campus-option {
            width: 100%;
            border: none;
            background: transparent;
            text-align: left;
            padding: 10px 12px;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            gap: 2px;
            transition: background 0.15s;
        }

        .campus-option:hover {
            background: #f3f6ef;
        }

        .campus-option.selected {
            background: #eaf1e3;
        }

        .campus-option strong {
            font-size: 13px;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .campus-option span {
            font-size: 11.5px;
            color: var(--text-muted);
        }

        .top-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Pickup / Delivery Mode Switcher */
        .mode-switch {
            display: inline-flex;
            align-items: center;
            background: #eef2ea;
            border: 1px solid var(--border-color);
            border-radius: 11px;
            padding: 3px;
            gap: 2px;
        }

        .mode-btn {
            border: none;
            background: transparent;
            color: var(--text-muted);
            font-size: 12px;
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 8px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.16s ease;
        }

        .mode-btn:hover {
            color: var(--text-dark);
        }

        .mode-btn.active {
            background: #ffffff;
            color: var(--forest);
            box-shadow: 0 2px 6px rgba(24, 39, 28, 0.08);
        }

        .drawer-mode-tabs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: #eef2ea;
            border: 1px solid #dfe6da;
            border-radius: 12px;
            padding: 4px;
            gap: 4px;
        }

        .drawer-mode-tab {
            border: none;
            background: transparent;
            padding: 9px 10px;
            border-radius: 9px;
            font-size: 12px;
            font-weight: 800;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.16s ease;
            text-align: center;
        }

        .drawer-mode-tab.active {
            background: var(--forest);
            color: #ffffff;
            box-shadow: 0 3px 10px rgba(30, 61, 41, 0.18);
        }

        .delivery-input {
            width: 100%;
            margin-top: 8px;
            padding: 8px 11px;
            border-radius: 8px;
            border: 1px solid #d5dfd0;
            background: #ffffff;
            font-size: 12.5px;
            color: var(--text-dark);
            outline: none;
        }

        .delivery-input:focus {
            border-color: var(--forest);
        }

        .help-btn {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            background: #ffffff;
            color: #556257;
            font-weight: 700;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.15s;
        }

        .help-btn:hover {
            background: #f2f5ee;
            border-color: #d0d7cd;
        }

        .bag-btn {
            height: 38px;
            padding: 0 16px;
            border-radius: 10px;
            background-color: var(--forest);
            color: #ffffff;
            border: none;
            font-weight: 700;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: transform 0.15s, background 0.15s;
        }

        .bag-btn:hover {
            background-color: var(--forest-hover);
        }

        .bag-btn.bump {
            transform: scale(1.08);
        }

        /* Page Headers */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 22px;
        }

        .header-title h1 {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.8px;
            color: var(--text-dark);
            margin-bottom: 4px;
        }

        .header-title p {
            font-size: 13.5px;
            color: var(--text-muted);
        }

        .header-tag {
            font-size: 11.5px;
            color: #7c897e;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px 10px;
            border-radius: 999px;
            transition: background 0.15s;
            text-decoration: none;
        }

        .header-tag:hover {
            background: #eff3eb;
            color: var(--forest);
        }

        /* Hero Banner */
        .hero-banner {
            background: linear-gradient(135deg, #e7efdd 0%, #edf4e5 100%);
            border-radius: 24px;
            padding: 36px 44px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            margin-bottom: 28px;
            border: 1px solid #dfe7d6;
            transition: background 0.35s ease;
        }

        .banner-content {
            max-width: 500px;
            z-index: 2;
        }

        .banner-eyebrow {
            font-size: 10.5px;
            font-weight: 800;
            letter-spacing: 1.8px;
            text-transform: uppercase;
            color: #5d6f5f;
            margin-bottom: 12px;
        }

        .banner-content h2 {
            font-size: 35px;
            font-weight: 800;
            line-height: 1.12;
            letter-spacing: -1.2px;
            color: var(--text-dark);
            margin-bottom: 12px;
        }

        .banner-content p {
            font-size: 13px;
            color: #556357;
            margin-bottom: 22px;
            line-height: 1.5;
        }

        .banner-cta {
            background-color: var(--forest);
            color: #ffffff;
            border: none;
            padding: 11px 20px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.15s;
            text-decoration: none;
        }

        .banner-cta:hover {
            background-color: var(--forest-hover);
            transform: translateY(-1px);
        }

        .banner-visual {
            position: relative;
            width: 240px;
            height: 195px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
            cursor: pointer;
        }

        .glow-circle {
            position: absolute;
            width: 215px;
            height: 215px;
            background-color: #d6e5c5;
            border-radius: 50%;
            z-index: 0;
            opacity: 0.85;
            transition: transform 0.3s ease;
        }

        .banner-visual:hover .glow-circle {
            transform: scale(1.05);
        }

        .bowl-svg {
            position: relative;
            z-index: 1;
            filter: drop-shadow(0 16px 22px rgba(45, 65, 45, 0.14));
            transition: transform 0.3s ease;
        }

        .banner-visual:hover .bowl-svg {
            transform: translateY(-4px) rotate(-3deg);
        }

        .banner-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: rgba(30, 61, 41, 0.55);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 4;
            padding: 6px;
            border-radius: 6px;
            box-shadow: none;
            transition: all 0.2s ease;
        }

        .banner-arrow:hover {
            background: none;
            color: var(--forest);
            transform: translateY(-50%) scale(1.2);
            box-shadow: none;
        }

        .banner-arrow.prev {
            left: 10px;
        }

        .banner-arrow.next {
            right: 10px;
        }

        .banner-dots {
            position: absolute;
            bottom: 16px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 6px;
            z-index: 3;
        }

        .banner-dot {
            width: 8px;
            height: 4px;
            border-radius: 2px;
            background: #b5c7aa;
            border: none;
            cursor: pointer;
            padding: 0;
            transition: all 0.2s ease;
        }

        .banner-dot.active {
            width: 22px;
            background: var(--forest);
        }

        /* Controls */
        .controls-row {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 16px;
        }

        .search-container {
            flex: 1;
            position: relative;
        }

        .search-input {
            width: 100%;
            height: 44px;
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 0 38px 0 16px;
            font-size: 13.5px;
            color: var(--text-dark);
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .search-input::placeholder {
            color: #9da89f;
        }

        .search-input:focus {
            border-color: var(--forest);
            box-shadow: 0 0 0 3px rgba(30, 61, 41, 0.08);
        }

        .clear-search {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: #eef2ea;
            color: #5b685d;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            font-size: 12px;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .clear-search.visible {
            display: flex;
        }

        .sort-select {
            height: 44px;
            min-width: 165px;
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 0 14px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-dark);
            cursor: pointer;
            outline: none;
        }

        .sort-select:focus {
            border-color: var(--forest);
        }

        /* Category Filter Pills */
        .categories-pills {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 32px;
            overflow-x: auto;
            padding-bottom: 4px;
        }

        .cat-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            height: 38px;
            padding: 0 16px;
            border-radius: 999px;
            font-size: 12.5px;
            font-weight: 700;
            border: 1px solid var(--pill-inactive-border);
            background: var(--pill-inactive-bg);
            color: #4a574d;
            cursor: pointer;
            transition: all 0.15s;
            white-space: nowrap;
        }

        .cat-pill:hover {
            border-color: #cbd4c7;
            background: #f5f8f3;
        }

        .cat-pill.active {
            background-color: var(--pill-active-bg);
            color: #ffffff;
            border-color: var(--pill-active-bg);
        }

        /* Products Grid */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 18px;
        }

        .section-header h3 {
            font-size: 19px;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: var(--text-dark);
        }

        .section-header p {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 3px;
        }

        .fresh-picks {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1.4px;
            color: #7c8b7e;
            text-transform: uppercase;
            background: none;
            border: none;
            cursor: pointer;
        }

        .fresh-picks:hover {
            color: var(--forest);
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .product-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 18px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.18s, box-shadow 0.18s;
            cursor: pointer;
        }

        .product-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 26px rgba(26, 43, 30, 0.06);
        }

        .product-thumb {
            height: 165px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .product-thumb.warm-peach {
            background: linear-gradient(135deg, #f6e7d7, #efd6be);
        }

        .product-thumb.sage-green {
            background: linear-gradient(135deg, #e4eedc, #d4e4c8);
        }

        .product-thumb.warm-sand {
            background: linear-gradient(135deg, #f1e3d3, #e5cdb3);
        }

        .product-thumb.soft-mint {
            background: linear-gradient(135deg, #e2efe9, #cde3da);
        }

        .product-thumb.cream-gold {
            background: linear-gradient(135deg, #f5ecd7, #eadbb9);
        }

        .product-thumb.berry-cream {
            background: linear-gradient(135deg, #f3e4e2, #e8ceca);
        }

        .thumb-tag {
            position: absolute;
            top: 12px;
            left: 12px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 999px;
            padding: 5px 10px;
            font-size: 9.5px;
            font-weight: 800;
            letter-spacing: 0.7px;
            color: #36483a;
            text-transform: uppercase;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .like-btn {
            position: absolute;
            top: 12px;
            right: 12px;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.95);
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #6e7c70;
            font-size: 14px;
            transition: transform 0.15s, color 0.15s;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            z-index: 2;
        }

        .like-btn:hover {
            transform: scale(1.1);
            color: #d94343;
        }

        .like-btn.liked {
            color: #e03b3b;
            background: #fff5f5;
        }

        .product-body {
            padding: 16px 18px 18px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .product-stall-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .product-stall {
            font-size: 11px;
            font-weight: 700;
            color: #7b8a7d;
        }

        .product-rating {
            font-size: 11px;
            font-weight: 800;
            color: var(--forest);
            background: #eef3ea;
            padding: 2px 7px;
            border-radius: 999px;
        }

        .product-title {
            font-size: 15.5px;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 6px;
            letter-spacing: -0.3px;
        }

        .product-meta {
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 14px;
            line-height: 1.45;
        }

        .product-footer {
            margin-top: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 12px;
            border-top: 1px solid #f0f3ed;
        }

        .product-price {
            font-size: 15px;
            font-weight: 800;
            color: var(--forest);
        }

        .product-prep {
            font-size: 11px;
            color: #879489;
            font-weight: 600;
            margin-left: 6px;
        }

        .add-cart-btn {
            background: #eaf1e3;
            color: var(--forest);
            border: none;
            padding: 7px 14px;
            border-radius: 9px;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.15s;
        }

        .add-cart-btn:hover {
            background: var(--forest);
            color: #ffffff;
        }

        .add-cart-btn.added {
            background: var(--forest);
            color: var(--lime);
        }

        /* Empty State */
        .empty-state {
            background: #ffffff;
            border: 1px dashed #cfd8cb;
            border-radius: 20px;
            padding: 48px 24px;
            text-align: center;
            grid-column: 1 / -1;
        }

        .empty-state h4 {
            font-size: 17px;
            font-weight: 800;
            margin: 12px 0 6px;
        }

        .empty-state p {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 16px;
        }

        .empty-state a,
        .empty-state button {
            background: var(--forest);
            color: #ffffff;
            border: none;
            padding: 10px 18px;
            border-radius: 10px;
            font-size: 12.5px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        /* Orders Page */
        .orders-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .order-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 18px;
            padding: 22px 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .order-card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #f0f3ed;
            padding-bottom: 14px;
        }

        .order-code-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #eaf1e3;
            color: var(--forest);
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12.5px;
            font-weight: 800;
        }

        .order-status-steps {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 700;
            color: var(--forest);
        }

        .order-items-summary {
            display: flex;
            flex-direction: column;
            gap: 8px;
            font-size: 13.5px;
        }

        .order-item-line {
            display: flex;
            justify-content: space-between;
            color: #364539;
        }

        .order-card-foot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 12px;
            border-top: 1px solid #f0f3ed;
        }

        .order-actions {
            display: flex;
            gap: 8px;
        }

        .btn-outline {
            background: #ffffff;
            border: 1px solid var(--border-color);
            color: var(--text-dark);
            padding: 8px 14px;
            border-radius: 9px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-outline:hover {
            background: #f3f6f0;
        }

        .btn-primary-sm {
            background: var(--forest);
            border: none;
            color: #ffffff;
            padding: 8px 14px;
            border-radius: 9px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
        }

        /* Bag Drawer */
        .drawer-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(20, 32, 23, 0.36);
            backdrop-filter: blur(2px);
            z-index: 90;
            display: none;
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .drawer-backdrop.open {
            display: block;
            opacity: 1;
        }

        .bag-drawer {
            position: fixed;
            top: 0;
            right: 0;
            width: min(420px, 100vw);
            height: 100vh;
            background: #ffffff;
            z-index: 100;
            box-shadow: -16px 0 40px rgba(20, 34, 24, 0.14);
            display: flex;
            flex-direction: column;
            transform: translateX(100%);
            transition: transform 0.26s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .bag-drawer.open {
            transform: translateX(0);
        }

        .drawer-header {
            padding: 22px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .drawer-header h3 {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: -0.4px;
        }

        .close-drawer-btn {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: 1px solid var(--border-color);
            background: #ffffff;
            cursor: pointer;
            font-size: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .drawer-body {
            flex: 1;
            overflow-y: auto;
            padding: 20px 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .pickup-box {
            background: #f4f7f0;
            border: 1px solid #e2e9dc;
            border-radius: 14px;
            padding: 14px 16px;
            font-size: 12.5px;
        }

        .pickup-box strong {
            display: block;
            color: var(--forest);
            margin-bottom: 6px;
        }

        .pickup-select {
            width: 100%;
            margin-top: 8px;
            padding: 8px 10px;
            border-radius: 8px;
            border: 1px solid #d5dfd0;
            background: #ffffff;
            font-size: 12.5px;
            font-weight: 600;
        }

        .bag-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding-bottom: 14px;
            border-bottom: 1px solid #f0f3ed;
        }

        .bag-item-info h5 {
            font-size: 13.5px;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .bag-item-info p {
            font-size: 11.5px;
            color: var(--text-muted);
        }

        .bag-item-info .item-price {
            font-size: 12.5px;
            font-weight: 800;
            color: var(--forest);
            margin-top: 4px;
        }

        .qty-controls {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f3f6ef;
            padding: 4px 6px;
            border-radius: 9px;
        }

        .qty-btn {
            width: 24px;
            height: 24px;
            border: none;
            border-radius: 6px;
            background: #ffffff;
            color: var(--text-dark);
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qty-val {
            font-size: 12.5px;
            font-weight: 800;
            min-width: 16px;
            text-align: center;
        }

        .eco-check-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #eef4e6;
            border: 1px solid #d8e5c8;
            padding: 12px 14px;
            border-radius: 12px;
            font-size: 12.5px;
            cursor: pointer;
        }

        .eco-check-row input {
            accent-color: var(--forest);
            width: 16px;
            height: 16px;
        }

        .drawer-footer {
            padding: 18px 24px 22px;
            border-top: 1px solid var(--border-color);
            background: #fbfcfa;
        }

        .summary-line {
            display: flex;
            justify-content: space-between;
            font-size: 12.5px;
            color: var(--text-muted);
            margin-bottom: 6px;
        }

        .summary-line.total {
            font-size: 15.5px;
            font-weight: 800;
            color: var(--text-dark);
            margin: 10px 0 14px;
            padding-top: 8px;
            border-top: 1px solid var(--border-color);
        }

        .checkout-btn {
            width: 100%;
            height: 48px;
            border: none;
            border-radius: 12px;
            background: var(--forest);
            color: #ffffff;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            transition: background 0.15s;
        }

        .checkout-btn:hover {
            background: var(--forest-hover);
        }

        .checkout-btn:disabled {
            background: #b8c2b9;
            cursor: not-allowed;
        }

        /* Modal */
        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(20, 32, 23, 0.42);
            backdrop-filter: blur(3px);
            z-index: 110;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-backdrop.open {
            display: flex;
        }

        .modal-card {
            background: #ffffff;
            width: min(480px, 100%);
            border-radius: 22px;
            padding: 28px;
            box-shadow: 0 24px 48px rgba(18, 32, 22, 0.18);
            position: relative;
        }

        .modal-card h3 {
            font-size: 21px;
            font-weight: 800;
            letter-spacing: -0.6px;
            margin-bottom: 8px;
        }

        .modal-card p {
            font-size: 13.5px;
            color: var(--text-muted);
            line-height: 1.55;
            margin-bottom: 16px;
        }

        .faq-step {
            background: #f5f8f2;
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 10px;
            font-size: 12.5px;
        }

        .faq-step strong {
            display: block;
            color: var(--forest);
            margin-bottom: 3px;
        }

        /* Toast Notification */
        .toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: #1b2e21;
            color: #ffffff;
            padding: 12px 18px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            box-shadow: 0 12px 28px rgba(18, 34, 22, 0.24);
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 150;
            transform: translateY(80px);
            opacity: 0;
            pointer-events: none;
            transition: all 0.25s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .toast.show {
            transform: translateY(0);
            opacity: 1;
            pointer-events: auto;
        }

        .toast-action {
            background: var(--lime);
            color: var(--forest);
            border: none;
            padding: 5px 10px;
            border-radius: 7px;
            font-size: 11.5px;
            font-weight: 800;
            cursor: pointer;
        }

        @media (max-width: 1024px) {
            .products-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }

            aside.sidebar {
                width: 100%;
                height: auto;
                position: static;
                border-right: none;
                border-bottom: 1px solid var(--border-color);
            }

            main.main-content {
                padding: 20px;
            }

            .hero-banner {
                flex-direction: column;
                padding: 26px 20px;
            }

            .banner-visual {
                margin-top: 20px;
            }

            .products-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <a href="index.php" class="logo-area">
            <div class="logo-badge">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M12 2.5c.7 4.6 2.9 6.8 7.5 7.5-4.6.7-6.8 2.9-7.5 7.5-.7-4.6-2.9-6.8-7.5-7.5 4.6-.7 6.8-2.9 7.5-7.5Z" />
                </svg>
            </div>
            <div class="logo-text">
                <div class="logo-title">unifood<span class="dot">✦</span></div>
                <div class="logo-tagline">Good Food. Good Mood.</div>
            </div>
        </a>

        <div class="nav-links">
            <a href="index.php" class="nav-item <?= $activePage === 'explore' ? 'active' : '' ?>">
                <span class="nav-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M12 2.5c.7 4.6 2.9 6.8 7.5 7.5-4.6.7-6.8 2.9-7.5 7.5-.7-4.6-2.9-6.8-7.5-7.5 4.6-.7 6.8-2.9 7.5-7.5Z" />
                    </svg>
                    Explore food
                </span>
            </a>
            <a href="orders.php" class="nav-item <?= $activePage === 'orders' ? 'active' : '' ?>">
                <span class="nav-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                    </svg>
                    My orders
                </span>
                <span class="nav-badge" id="orders-badge">0</span>
            </a>
            <a href="favorites.php" class="nav-item <?= $activePage === 'favorites' ? 'active' : '' ?>">
                <span class="nav-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z">
                        </path>
                    </svg>
                    Favorites
                </span>
                <span class="nav-badge" id="favorites-badge">0</span>
            </a>
            <?php if (session()->has('role') && strtolower(session('role')) === 'seller'): ?>
            <a href="vendor.php" class="nav-item <?= $activePage === 'vendor' ? 'active' : '' ?>">
                <span class="nav-item-left">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    Vendor portal
                </span>
                <span class="nav-badge" style="background:#e8f4e6; color:#275b36; font-size:10px; font-weight:800; padding:2px 6px; border-radius:999px;">STALL</span>
            </a>
            <?php endif; ?>
        </div>

        <div class="sidebar-eco" id="sidebar-eco-card" onclick="toggleEcoPledge()"
            title="Click to toggle your BYO Container discount">
            <div class="eco-top">
                <span class="eco-icon">🌱</span>
                <span class="eco-status-pill" id="eco-pill-label">Tap to join</span>
            </div>
            <h4>A little greener, together.</h4>
            <p>Bring your own container and make your next meal a little kinder.</p>
            <span class="eco-highlight">Small choices. Big impact.</span>
        </div>

        <div class="sidebar-user">
            <div class="user-left" onclick="openProfileModal()" title="View student account details">
                <div class="user-avatar"><?= htmlspecialchars($initials, ENT_QUOTES, 'UTF-8') ?></div>
                <div class="user-info">
                    <h5><?= htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') ?></h5>
                    <p><?= htmlspecialchars($role, ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            </div>
            <form method="post" action="{{ route('logout') }}">
@csrf
                <input type="hidden" name="action" value="logout">
                <button class="logout-btn" type="submit" title="Sign out">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN CONTENT WRAPPER -->
    <main class="main-content">
        <!-- Top Navbar -->
        <header class="top-nav">
            <div class="campus-selector-wrap">
                <button type="button" class="campus-selector" id="campus-trigger" onclick="toggleCampusMenu(event)">
                    <div>Your campus <span class="campus-dropdown"><span id="current-campus-name">Main Campus</span>
                            ▾</span></div>
                    <div class="sub-location"><span class="sparkle">✦</span> <span id="current-court-name">Cafe Abang
                            Mirul</span></div>
                </button>
                <div class="campus-menu" id="campus-menu">
                    <button type="button" class="campus-option selected"
                        onclick="selectCampus('Main Campus', 'Cafe Abang Mirul', this)">
                        <strong>Main Campus <span>✓</span></strong>
                        <span>Cafe Abang Mirul · 6 stalls open</span>
                    </button>
                    <button type="button" class="campus-option"
                        onclick="selectCampus('Engineering Campus', 'Tech Hub Cafe', this)">
                        <strong>AL Baek Cafe</strong>
                        <span>Tech Hub Cafe · 5 stalls open</span>
                    </button>
                    <button type="button" class="campus-option"
                        onclick="selectCampus('Medical & Health Campus', 'Atrium Dining Hall', this)">
                        <strong>Medical & Health Campus</strong>
                        <span>Atrium Dining Hall · 4 stalls open</span>
                    </button>
                    <button type="button" class="campus-option"
                        onclick="selectCampus('Student Residential Hall', 'Night Market Court', this)">
                        <strong>Student Residential Hall</strong>
                        <span>Night Market Court · Late night bites</span>
                    </button>
                </div>
            </div>

            <div class="top-actions">
                <div class="mode-switch" role="group" aria-label="Order fulfillment mode">
                    <button type="button" class="mode-btn active" id="top-mode-pickup" onclick="setOrderMode('pickup')">
                        <span>🏃</span> Pickup
                    </button>
                    <button type="button" class="mode-btn" id="top-mode-delivery" onclick="setOrderMode('delivery')">
                        <span>🛵</span> Delivery
                    </button>
                </div>
                <button class="help-btn" type="button" title="How UniFood works" onclick="openHelpModal()">?</button>
                <button class="bag-btn" id="bag-button" type="button" onclick="openBagDrawer()">
                    <span id="bag-btn-label">Bag 0</span> ↗
                </button>
            </div>
        </header>