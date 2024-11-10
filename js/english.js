let questions = [
    {
        question: "What is the capital of France?",
        options: ["Berlin", "Madrid", "Paris", "Rome"],
        correctAnswer: 2
    },
    {
        question: "What is 2 + 2?",
        options: ["3", "4", "5", "6"],
        correctAnswer: 1
    },
    {
        question: "Who developed the theory of relativity?",
        options: ["Newton", "Einstein", "Galileo", "Darwin"],
        correctAnswer: 1
    },
    {
        question: "Which planet is known as the Red Planet?",
        options: ["Earth", "Mars", "Venus", "Jupiter"],
        correctAnswer: 1
    },
    {
        question: "What is the largest ocean on Earth?",
        options: ["Atlantic", "Indian", "Arctic", "Pacific"],
        correctAnswer: 3
    },
    {
        question: "Who wrote 'Romeo and Juliet'?",
        options: ["Shakespeare", "Dickens", "Hemingway", "Austen"],
        correctAnswer: 0
    },
    {
        question: "What is the largest continent?",
        options: ["Africa", "Asia", "Europe", "North America"],
        correctAnswer: 1
    },
    {
        question: "Who painted the Mona Lisa?",
        options: ["Van Gogh", "Picasso", "Da Vinci", "Michelangelo"],
        correctAnswer: 2
    },
    {
        question: "What is the speed of light?",
        options: ["3.0 × 10^8 m/s", "3.0 × 10^6 m/s", "2.9 × 10^8 m/s", "3.5 × 10^8 m/s"],
        correctAnswer: 0
    },
    {
        question: "What is the chemical symbol for gold?",
        options: ["Au", "Ag", "Pb", "Fe"],
        correctAnswer: 0
    }
];

const urlParams = new URLSearchParams(window.location.search);
const subject = urlParams.get('subject');

// Set the quiz title and content based on the selected subject
document.getElementById('quiz-subject').textContent = subject.charAt(0).toUpperCase() + subject.slice(1);

let currentQuestionIndex = 0;
let score = 0;
let timer = 900; // 15 minutes in seconds
let timerInterval;

function startTimer() {
    timerInterval = setInterval(() => {
        timer--;
        document.getElementById("time").textContent = timer;
        if (timer <= 0) {
            clearInterval(timerInterval);
            endQuiz();
        }
    }, 1000);
}

function loadQuestion() {
    const questionObj = questions[currentQuestionIndex];
    document.getElementById("question").textContent = questionObj.question;

    const optionsList = document.getElementById("options");
    optionsList.innerHTML = "";

    questionObj.options.forEach((option, index) => {
        const li = document.createElement("li");
        const button = document.createElement("button");
        button.textContent = option;
        button.onclick = () => handleAnswer(index);
        li.appendChild(button);
        optionsList.appendChild(li);
    });
}

function handleAnswer(selectedIndex) {
    const questionObj = questions[currentQuestionIndex];
    const nextButton = document.getElementById("next-btn");

    if (selectedIndex === questionObj.correctAnswer) {
        score++;
    }

    nextButton.classList.remove("hidden");
}

function nextQuestion() {
    const nextButton = document.getElementById("next-btn");
    nextButton.classList.add("hidden");

    currentQuestionIndex++;
    if (currentQuestionIndex < questions.length) {
        loadQuestion();
    } else {
        endQuiz();
    }
}

function endQuiz() {
    document.getElementById("question").classList.add("hidden");
    document.getElementById("options").classList.add("hidden");
    document.getElementById("next-btn").classList.add("hidden");

    const resultDiv = document.getElementById("result");
    resultDiv.classList.remove("hidden");
    document.getElementById("score").textContent = score;
}

// Start quiz
startTimer();
loadQuestion();