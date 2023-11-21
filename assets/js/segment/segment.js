const SEGMENT_WIDTH = "100%";
const SEGMENT_HEIGHT = "60px";
const palette = [
    "#c6e6ff",
    "#96d0ff",
    "#6cb6ff",
    "#539bf5",
    "#4184e4",
    "#316dca",
    "#255ab2",
    "#1b4b91",
    "#143d79",
    "#0f2d5c"
];

function buildSegmentBar(element, options) {
    let percentages = getSegmentPercentages(options.data);

    for (let i = 0; i < options.data.length; i++) {
        options.data[i].percent = +percentages[i];
    }

    element.style.width = options.width ? options.width : SEGMENT_WIDTH;
    element.style.height = options.height ? options.height : SEGMENT_HEIGHT;
    element.classList.add("segment-bar");
    let colorIt = getSegmentNextColor();

    for (let item of options.data) {
        let div = document.createElement("div");

        // Prepare wrapper
        div.style.width = `${parseFloat(item.percent * 100)}%`;
        div.style.backgroundColor = item.color
            ? item.color
            : colorIt.next().value;
        div.classList.add("segment-item-wrapper");

        // Percentage span
        let span = document.createElement("span");
        span.textContent = `${prettifySegmentPercentage(item.percent * 100)}%`;
        span.classList.add("segment-item-percentage");

        // Value span
        let valueSpan = document.createElement("span");
        valueSpan.textContent = `${item.value.toLocaleString("en-US")}`;
        valueSpan.classList.add("segment-item-value");

        // Title span
        if (item.title && item.title.length > 0) {
            let titleSpan = document.createElement("span");
            titleSpan.textContent = item.title;
            titleSpan.classList.add("segment-item-title");
            div.appendChild(titleSpan);

            div.title = `${item.title} (${item.value})`;
        }

        div.appendChild(span);
        div.appendChild(valueSpan);
        element.appendChild(div);
    }
}

function prettifySegmentPercentage(percentage) {
    let pretty = parseFloat(percentage).toFixed(2);
    let v = pretty.split(".");
    let final = 0;
    if (v[1]) {
        let digits = v[1].split("");
        if (digits[0] == 0 && digits[1] == 0) {
            final = parseFloat(`${v[0]}`);
        } else {
            final = pretty;
        }
    } else {
        final = parseFloat(v[0]);
    }
    return final;
}

// Accepts an array of chart data, returns an array of percentages
function getSegmentPercentages(data) {
    let sum = getSegmentSum(data);

    return data.map(function (item) {
        return parseFloat(item.value / sum);
    });
}

// Accepts an array of chart data, returns the sum of all values
function getSegmentSum(data) {
    return data.reduce(function (sum, item) {
        return sum + item.value;
    }, 0);
}

function* getSegmentNextColor() {
    let i = 0;
    while (true) {
        yield palette[i];
        i = (i + 1) % palette.length;
    }
}
