function checkPositiveNumber(number) {
    try {
        if (number < 0) {
            throw new Error("The number must be positive.");
        }
        console.log("The number is positive.");
    } catch (error) {
        console.log("Caught an error:", error.message);
    }
}
checkPositiveNumber(-5);
checkPositiveNumber(10);